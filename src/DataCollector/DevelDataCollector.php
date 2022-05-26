<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\Link;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\devel\Plugin\Menu\DestinationMenuLink;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Provides a toolbar item for Devel menu links.
 */
class DevelDataCollector extends DataCollector {

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $this->data['original_route'] = \Drupal::routeMatch()->getRouteName();
  }

  /**
   * Return the list of Devel links.
   *
   * @return array
   *   The list of Devel links.
   */
  public function getLinks(): array {
    return $this->develMenuLinks($this->data['original_route']);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'devel';
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * Return the list of Devel links for a given route.
   *
   * @param string $original_route
   *   The original route.
   *
   * @return array
   *   Array containing Devel Menu links
   */
  protected function develMenuLinks(string $original_route): array {
    // We cannot use injected services here because at this point this
    // class is deserialized from a storage and not constructed.
    $menuLinkTreeService = \Drupal::service('menu.link_tree');
    $rendererService = \Drupal::service('renderer');

    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(1)->onlyEnabledLinks();
    $tree = $menuLinkTreeService->load('devel', $parameters);

    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $menuLinkTreeService->transform($tree, $manipulators);

    $links = array();
    foreach ($tree as $item) {
      /** @var DestinationMenuLink $item_link */
      $item_link = $item->link;

      // Get the link url and replace the destination parameter with the
      // original route.
      $url = $item_link->getUrlObject();
      $url->setOption('query', ['destination' => Url::fromRoute($original_route)->toString()]);

      // Build and render the link.
      $link = Link::fromTextAndUrl($item_link->getTitle(), $url);
      $rendered = $rendererService->renderPlain($link->toRenderable());

      $links[] = Markup::create($rendered);;
    }

    return $links;
  }
}
