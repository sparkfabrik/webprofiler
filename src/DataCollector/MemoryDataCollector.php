<?php

namespace Drupal\webprofiler\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Collects memory data.
 * */
class MemoryDataCollector extends DataCollector implements LateDataCollectorInterface {

  use DataCollectorTrait;

  public function __construct() {
    $this->reset();
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $this->updateMemoryUsage();
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [
      'memory' => 0,
      'memory_limit' => $this->convertToBytes(ini_get('memory_limit')),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function lateCollect() {
    $this->updateMemoryUsage();
  }

  /**
   * @return int
   */
  public function getMemory(): int {
    return $this->data['memory'];
  }

  /**
   * @return int|float
   */
  public function getMemoryLimit(): int|float {
    return $this->data['memory_limit'];
  }

  /**
   * @return void
   */
  public function updateMemoryUsage() {
    $this->data['memory'] = memory_get_peak_usage(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'memory';
  }

}
