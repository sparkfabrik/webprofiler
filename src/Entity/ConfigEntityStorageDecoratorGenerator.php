<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Entity;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\PhpStorage\PhpStorageFactory;
use Drupal\webprofiler\DecoratorGeneratorInterface;
use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\BuilderFactory;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

/**
 * Generate decorators for config entity storage classes.
 */
class ConfigEntityStorageDecoratorGenerator implements DecoratorGeneratorInterface {

  /**
   * DecoratorGenerator constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The Entity type manager service.
   */
  public function __construct(
    protected readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function generate(): void {
    $classes = $this->getClasses();

    foreach ($classes as $class) {
      try {
        $methods = $this->getMethods($class);
        $body = $this->createDecorator($class, $methods);
        $this->writeDecorator($class['id'], $body);
      }
      catch (\Exception $e) {
        throw new \Exception('Unable to generate decorator for class ' . $class['class'] . '. ' . $e->getMessage());
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDecorators(): array {
    $classes = $this->getClasses();

    return array_map(function ($class) {
      return $class['decoratorClass'];
    }, $classes);
  }

  /**
   * Return information about every config entity storage classes.
   *
   * @return array
   *   Information about every config entity storage classes.
   */
  private function getClasses(): array {
    $definitions = $this->entityTypeManager->getDefinitions();
    $classes = [];

    foreach ($definitions as $definition) {
      try {
        $classPath = $this->getClassPath($definition->getStorageClass());
        $ast = $this->getAst($classPath);

        $visitor = new FindingVisitor(function (Node $node) {
          return $this->isConfigEntityStorage($node);
        });

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->addVisitor(new NameResolver());
        $traverser->traverse($ast);

        $nodes = $visitor->getFoundNodes();

        /** @var \PhpParser\Node\Stmt\Class_ $node */
        foreach ($nodes as $node) {
          $classes[$definition->id()] = [
            'id' => $definition->id(),
            'class' => $node->name->name,
            'interface' => '\\' . implode('\\', $node->implements[0]->getParts()),
            'decoratorClass' => '\\Drupal\\webprofiler\\Entity\\' . $node->name->name . 'Decorator',
          ];
        }
      }
      catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return [];
      }
      catch (\ReflectionException $error) {
        echo "Reflection error: {$error->getMessage()}\n";
        return [];
      }
    }

    return $classes;
  }

  /**
   * Get the filename of the file in which the class has been defined.
   *
   * @param string $class
   *   A class name.
   *
   * @return string
   *   The filename of the file in which the class has been defined.
   *
   * @throws \ReflectionException
   */
  private function getClassPath(string $class): string {
    $reflector = new \ReflectionClass($class);

    return $reflector->getFileName();
  }

  /**
   * Parses PHP code into a node tree.
   *
   * @param string $classPath
   *   The filename of the file in which a class has been defined.
   *
   * @return \PhpParser\Node\Stmt[]|null
   *   Array of statements.
   */
  private function getAst(string $classPath): ?array {
    $code = file_get_contents($classPath);
    $parser = (new ParserFactory())->createForHostVersion();

    return $parser->parse($code);
  }

  /**
   * Return TRUE if this Node represents a config entity storage class.
   *
   * @param \PhpParser\Node $node
   *   The Node to check.
   *
   * @return bool
   *   TRUE if this Node represents a config entity storage class.
   */
  private function isConfigEntityStorage(Node $node): bool {
    if (!$node instanceof Class_) {
      return FALSE;
    }

    if ($node->extends !== NULL &&
      $node->extends->getParts()[0] == 'ConfigEntityStorage' &&
      isset($node->implements[0]) &&
      $node->implements[0]->getParts()[0] != ''
    ) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Create the decorator from class information.
   *
   * @param array $class
   *   The class information.
   *
   * @return \PhpParser\Node\Stmt\ClassMethod[]
   *   The methods of the class.
   *
   * @throws \Exception
   */
  private function getMethods(array $class): array {
    $classPath = $this->getClassPath($class['interface']);
    $ast = $this->getAst($classPath);

    $nodeFinder = new NodeFinder();

    /** @var \PhpParser\Node\Stmt\ClassMethod[] $nodes */
    $nodes = $nodeFinder->find($ast, function (Node $node) {
      return $node instanceof ClassMethod;
    });

    return $nodes;
  }

  /**
   * Create the decorator from class information and methods.
   *
   * @param array $class
   *   The class information.
   * @param \PhpParser\Node\Stmt\ClassMethod[] $methods
   *   The methods of the class.
   *
   * @return string
   *   The decorator class body.
   *
   * phpcs:disable Drupal.Classes.FullyQualifiedNamespace.UseStatementMissing
   */
  private function createDecorator(array $class, array $methods): string {
    $decorator = $class['class'] . 'Decorator';

    $factory = new BuilderFactory();
    $file = $factory
      ->namespace('Drupal\webprofiler\Entity')
      ->addStmt($factory->use('Drupal\webprofiler\Entity\ConfigEntityStorageDecorator'));

    $generated_class = $factory
      ->class($decorator)
      ->extend('ConfigEntityStorageDecorator')
      ->implement($class['interface'])
      ->setDocComment('/**
                                    * This file is auto-generated by the Webprofiler module.
                                    */',
      );

    foreach ($methods as $method) {
      $generated_class->addStmt($this->createMethod($method));
    }

    $file->addStmt($generated_class);

    $stmts = [$file->getNode()];
    $prettyPrinter = new PrettyPrinter\Standard();

    // Add a newline at the end of the file.
    return $prettyPrinter->prettyPrintFile($stmts) . "\n";
  }

  /**
   * Create a decorator method.
   *
   * @param \PhpParser\Node\Stmt\ClassMethod $method
   *   The method.
   *
   * @return \PhpParser\Builder\Method
   *   The generated method.
   */
  private function createMethod(ClassMethod $method): Method {
    $factory = new BuilderFactory();
    $generated_method = $factory->method($method->name->name)->makePublic();

    foreach ($method->getParams() as $param) {
      $generated_method->addParam($this->createParameter($param));
    }

    $generated_body = $factory->methodCall(
      new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), 'getOriginalObject()'),
      $method->name->name,
      array_map(function ($param) {
        return new Node\Expr\Variable($param->var->name);
      }, $method->getParams()),
    );

    // If return type is different from void, add a return statement.
    if (!$method->getReturnType() instanceof Node\Identifier || $method->getReturnType()->name != 'void') {
      $generated_body = new Node\Stmt\Return_($generated_body);
    }

    $generated_method->addStmt($generated_body);

    if ($method->getReturnType() != NULL) {
      $generated_method->setReturnType($method->getReturnType());
    }

    return $generated_method;
  }

  /**
   * Create a decorator method parameter.
   *
   * @param \PhpParser\Node\Param $param
   *   The method parameter.
   *
   * @return \PhpParser\Builder\Param
   *   The generated parameter.
   */
  private function createParameter(Node\Param $param): Param {
    $factory = new BuilderFactory();
    $generated_param = $factory
      ->param($param->var->name);

    if ($param->type != NULL) {
      $generated_param->setType($param->type);
    }

    if ($param->default != NULL) {
      $generated_param->setDefault($param->default);
    }

    if ($param->byRef) {
      $generated_param->makeByRef();
    }

    if ($param->variadic) {
      $generated_param->makeVariadic();
    }

    return $generated_param;
  }

  /**
   * Write a decorator class body to file.
   *
   * @param string $name
   *   The class name.
   * @param string $body
   *   The class body.
   */
  private function writeDecorator(string $name, string $body): void {
    $storage = PhpStorageFactory::get('webprofiler');

    if (!$storage->exists($name)) {
      $storage->save($name, $body);
    }
  }

}
