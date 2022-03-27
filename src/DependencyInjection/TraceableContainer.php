<?php

declare(strict_types=1);

namespace Drupal\webprofiler\DependencyInjection;

use Drupal\Component\Utility\Timer;
use Drupal\Core\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extends the Drupal container class to trace service instantiations.
 */
class TraceableContainer extends Container {

  /**
   * @var array
   */
  protected array $tracedData;

  /**
   * {@inheritdoc}
   */
  public function get($id, $invalid_behavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object {
    $stopwatch = parent::get('webprofiler.stopwatch');
    $stopwatch->openSection();

    if ('webprofiler.stopwatch' === $id) {
      return $stopwatch;
    }

    Timer::start($id);
    $e = $stopwatch->start($id, 'service');

    $service = parent::get($id, $invalid_behavior);

    $this->tracedData[$id] = Timer::stop($id);
    if ($e->isStarted()) {
      $e->stop();
    }

    return $service;
  }

  /**
   * @return array
   */
  public function getTracedData(): array {
    return $this->tracedData;
  }

}
