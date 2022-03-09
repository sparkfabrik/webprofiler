<?php

namespace Drupal\webprofiler\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects ajax requests.
 */
class AjaxDataCollector extends DataCollector {

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    // All collecting is done client side.
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    // All collecting is done client side.
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'ajax';
  }

}
