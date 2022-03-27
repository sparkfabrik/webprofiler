<?php

declare(strict_types=1);

namespace Drupal\webprofiler\EventDispatcher;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class TraceableEventDispatcher
 */
class TraceableEventDispatcher extends ContainerAwareEventDispatcher implements EventDispatcherTraceableInterface {

  /**
   * @var \Symfony\Component\Stopwatch\Stopwatch
   *   The stopwatch service.
   */
  protected Stopwatch $stopwatch;

  /**
   * @var array
   */
  protected array $calledListeners;

  /**
   * @var array
   */
  protected array $notCalledListeners;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container, array $listeners = []) {
    parent::__construct($container, $listeners);

    $this->notCalledListeners = $listeners;
  }

  /**
   * {@inheritdoc}
   */
  public function addListener($event_name, $listener, $priority = 0) {
    parent::addListener($event_name, $listener, $priority);

    $this->notCalledListeners[$event_name][$priority][] = ['callable' => $listener];
  }

  /**
   * {@inheritdoc}
   */
  public function dispatch(object $event, ?string $eventName = NULL): object {
    $event_name = $eventName ?? get_class($event);

    $this->beforeDispatch($event_name, $event);

    if (isset($this->listeners[$event_name])) {
      // Sort listeners if necessary.
      if (isset($this->unsorted[$event_name])) {
        krsort($this->listeners[$event_name]);
        unset($this->unsorted[$event_name]);
      }

      // Invoke listeners and resolve callables if necessary.
      foreach ($this->listeners[$event_name] as $priority => &$definitions) {
        foreach ($definitions as &$definition) {
          if (!isset($definition['callable'])) {
            $definition['callable'] = [
              $this->container->get($definition['service'][0]),
              $definition['service'][1],
            ];
          }
          if (is_array($definition['callable']) && isset($definition['callable'][0]) && $definition['callable'][0] instanceof \Closure) {
            $definition['callable'][0] = $definition['callable'][0]();
          }

          call_user_func($definition['callable'], $event, $event_name, $this);

          $this->addCalledListener($definition, $event_name, $priority);

          if ($event->isPropagationStopped()) {
            return $event;
          }
        }
      }
    }

    $this->afterDispatch($event_name, $event);

    return $event;
  }

  /**
   * {@inheritdoc}
   */
  public function getCalledListeners(): array {
    return $this->calledListeners;
  }

  /**
   * {@inheritdoc}
   */
  public function getNotCalledListeners(): array {
    return $this->notCalledListeners;
  }

  /**
   * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch
   */
  public function setStopwatch(Stopwatch $stopwatch) {
    $this->stopwatch = $stopwatch;
  }

  /**
   * Called before dispatching the event.
   *
   * @param string $eventName The event name
   * @param object $event The event
   */
  protected function beforeDispatch(string $eventName, object $event) {
    switch ($eventName) {
      case KernelEvents::REQUEST:
        $event->getRequest()->attributes->set('_stopwatch_token', substr(hash('sha256', uniqid(strval(mt_rand()), true)), 0, 6));
        $this->stopwatch->openSection();
        break;
      case KernelEvents::VIEW:
      case KernelEvents::RESPONSE:
        // stop only if a controller has been executed
        if ($this->stopwatch->isStarted('controller')) {
          $this->stopwatch->stop('controller');
        }
        break;
      case KernelEvents::TERMINATE:
        $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
        if (NULL === $sectionId) {
          break;
        }
        // There is a very special case when using built-in AppCache class as kernel wrapper, in the case
        // of an ESI request leading to a `stale` response [B]  inside a `fresh` cached response [A].
        // In this case, `$token` contains the [B] debug token, but the  open `stopwatch` section ID
        // is equal to the [A] debug token. Trying to reopen section with the [B] token throws an exception
        // which must be caught.
        try {
          $this->stopwatch->openSection($sectionId);
        }
        catch (\LogicException $e) {
        }
        break;
    }
  }

  /**
   * Called after dispatching the event.
   *
   * @param string $eventName The event name
   * @param object $event The event
   */
  protected function afterDispatch(string $eventName, object $event) {
    switch ($eventName) {
      case KernelEvents::CONTROLLER_ARGUMENTS:
        $this->stopwatch->start('controller', 'section');
        break;
      case KernelEvents::RESPONSE:
        $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
        if (NULL === $sectionId) {
          break;
        }
        $this->stopwatch->stopSection($sectionId);
        break;
      case KernelEvents::TERMINATE:
        // In the special case described in the `preDispatch` method above, the `$token` section
        // does not exist, then closing it throws an exception which must be caught.
        $sectionId = $event->getRequest()->attributes->get('_stopwatch_token');
        if (NULL === $sectionId) {
          break;
        }
        try {
          $this->stopwatch->stopSection($sectionId);
        }
        catch (\LogicException $e) {
        }
        break;
    }
  }

  /**
   * @param $definition
   * @param $event_name
   * @param $priority
   */
  private function addCalledListener($definition, $event_name, $priority) {
    if ($this->isClosure($definition['callable'])) {
      $this->calledListeners[$event_name][$priority][] = [
        'class' => 'Closure',
        'method' => '',
      ];
    }
    else {
      $this->calledListeners[$event_name][$priority][] = [
        'class' => get_class($definition['callable'][0]),
        'method' => $definition['callable'][1],
      ];
    }

    foreach ($this->notCalledListeners[$event_name][$priority] as $key => $listener) {
      if (isset($listener['service'])) {
        if ($listener['service'][0] == $definition['service'][0] && $listener['service'][1] == $definition['service'][1]) {
          unset($this->notCalledListeners[$event_name][$priority][$key]);
        }
      }
      else {
        if ($this->isClosure($listener['callable'])) {
          if (is_callable($listener['callable'], TRUE, $listenerCallableName) && is_callable($definition['callable'], TRUE, $definitionCallableName)) {
            if ($listenerCallableName == $definitionCallableName) {
              unset($this->notCalledListeners[$event_name][$priority][$key]);
            }
          }
        }
        else {
          if (get_class($listener['callable'][0]) == get_class($definition['callable'][0]) && $listener['callable'][1] == $definition['callable'][1]) {
            unset($this->notCalledListeners[$event_name][$priority][$key]);
          }
        }
      }

    }
  }

  /**
   * @param $t
   *
   * @return bool
   */
  private function isClosure($t): bool {
    return $t instanceof \Closure;
  }

}
