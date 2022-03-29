<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tracer\DependencyInjection\TraceableContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class ServicesDataCollector
 */
class ServicesDataCollector extends DataCollector implements HasPanelInterface {

  use StringTranslationTrait, DataCollectorTrait;

  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   *   $container
   */
  private ContainerInterface $container;

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'services';
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    if ($this->getServicesCount()) {
      $tracedData = [];
      if ($this->container instanceof TraceableContainer) {
        $tracedData = $this->container->getTracedData();
      }

      foreach (array_keys($this->getServices()) as $id) {
        $this->data['services'][$id]['initialized'] = $this->container->initialized($id);
        $this->data['services'][$id]['time'] = $tracedData[$id] ?? NULL;
      }
    }
  }

  /**
   * @param $services
   */
  public function setServices($services) {
    $this->data['services'] = $services;
  }

  /**
   * @return array
   */
  public function getServices(): array {
    return $this->data['services'];
  }

  /**
   * @return int
   */
  public function getServicesCount(): int {
    return count($this->getServices());
  }

  /**
   * @return array
   */
  public function getInitializedServices(): array {
    return array_filter($this->getServices(), function ($item) {
      return $item['initialized'];
    });
  }

  /**
   * @return int
   */
  public function getInitializedServicesCount(): int {
    return count($this->getInitializedServices());
  }

  /**
   * @return array
   */
  public function getInitializedServicesWithoutWebprofiler(): array {
    return array_filter($this->getInitializedServices(), function ($item) {
      return !str_starts_with($item['value']['id'], 'webprofiler');
    });
  }

  /**
   * @return int
   */
  public function getInitializedServicesWithoutWebprofilerCount(): int {
    return count($this->getInitializedServicesWithoutWebprofiler());
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPanel(): array {
    $data = $this->data;

    $http_middleware = array_filter($data['services'], function ($service) {
      return isset($service['value']['tags']['http_middleware']);
    });

    foreach ($http_middleware as &$service) {
      $service['value']['handle_method'] = $this->getMethodData($service['value']['class'], 'handle');
    }

    uasort($http_middleware, function ($a, $b) {
      $va = $a['value']['tags']['http_middleware'][0]['priority'];
      $vb = $b['value']['tags']['http_middleware'][0]['priority'];

      if ($va == $vb) {
        return 0;
      }
      return ($va > $vb) ? -1 : 1;
    });

    $data['http_middleware'] = $http_middleware;

    return $data;
  }

}
