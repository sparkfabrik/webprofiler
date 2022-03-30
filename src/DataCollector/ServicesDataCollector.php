<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\tracer\DependencyInjection\TraceableContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects data about services.
 */
class ServicesDataCollector extends DataCollector implements HasPanelInterface {

  use StringTranslationTrait, DataCollectorTrait;

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  private ContainerInterface $container;

  /**
   * ServicesDataCollector constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
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
   * Set services.
   *
   * @param array $services
   *   Array of services.
   */
  public function setServices(array $services) {
    $this->data['services'] = $services;
  }

  /**
   * Returns services.
   *
   * @return array
   *   Array of services.
   */
  public function getServices(): array {
    return $this->data['services'];
  }

  /**
   * Return the number of services.
   *
   * @return int
   *   The number of services.
   */
  public function getServicesCount(): int {
    return count($this->getServices());
  }

  /**
   * Returns array of services that are initialized.
   *
   * @return array
   *   Array of services that are initialized.
   */
  public function getInitializedServices(): array {
    return array_filter($this->getServices(), function ($item) {
      return $item['initialized'];
    });
  }

  /**
   * Returns the number of services that are initialized.
   *
   * @return int
   *   The number of services that are initialized.
   */
  public function getInitializedServicesCount(): int {
    return count($this->getInitializedServices());
  }

  /**
   * Return all services but the ones from Webprofiler itself.
   *
   * @return array
   *   All services but the ones from Webprofiler itself.
   */
  public function getInitializedServicesWithoutWebprofiler(): array {
    return array_filter($this->getInitializedServices(), function ($item) {
      return !str_starts_with($item['value']['id'], 'webprofiler');
    });
  }

  /**
   * Return the number of services but the ones from Webprofiler itself.
   *
   * @return int
   *   The number of services but the ones from Webprofiler itself.
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
