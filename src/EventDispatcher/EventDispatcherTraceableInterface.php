<?php

namespace Drupal\webprofiler\EventDispatcher;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 */
interface EventDispatcherTraceableInterface extends EventDispatcherInterface {

  /**
   * @return array
   */
  public function getCalledListeners(): array;

  /**
   * @return mixed
   */
  public function getNotCalledListeners(): array;

}
