<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Twig\Extension;

use Drupal\Core\Database\Database;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extensions to render database query information.
 */
class DatabaseExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('query_type', [$this, 'queryType']),
      new TwigFunction('query_executable', [$this, 'queryExecutable']),
    ];
  }

  /**
   * @param string $query
   *
   * @return string
   */
  public function queryType(string $query): string {
    $parts = explode(' ', $query);
    return strtoupper($parts[0]);
  }

  /**
   * @param array $query
   *
   * @return string
   */
  public function queryExecutable(array $query): string {
    $conn = Database::getConnection();

    $quoted = [];

    if (isset($query['args'])) {
      foreach ((array) $query['args'] as $key => $val) {
        $quoted[$key] = is_null($val) ? 'NULL' : $conn->quote($val);
      }
    }

    return strtr($query['query'], $quoted);
  }

}
