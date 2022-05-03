<?php

namespace Drupal\webprofiler\Profiler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\Profiler\FileProfilerStorage;
use Symfony\Component\HttpKernel\Profiler\Profiler as SymfonyProfiler;

/**
 * Extend the Symfony profiler to allow to choose the list of collectors.
 */
class Profiler extends SymfonyProfiler {

  /**
   * List of items to show in the toolbar.
   *
   * @var string[]
   */
  private array $activeToolbarItems;

  /**
   * Profiler constructor.
   *
   * @param \Symfony\Component\HttpKernel\Profiler\FileProfilerStorage $storage
   *   The profiler storage.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory service.
   */
  public function __construct(
    private readonly FileProfilerStorage $storage,
    private readonly LoggerInterface $logger,
    private readonly ConfigFactoryInterface $config
  ) {
    parent::__construct($storage, $logger);

    $this->activeToolbarItems = $this->config->get('webprofiler.settings')
      ->get('active_toolbar_items');
  }

  /**
   * {@inheritdoc}
   */
  public function add(DataCollectorInterface $collector) {
    // Drupal collector should not be disabled.
    if ($collector->getName() == 'drupal') {
      parent::add($collector);
    }
    else {
      if ($this->activeToolbarItems && array_key_exists($collector->getName(), $this->activeToolbarItems) && $this->activeToolbarItems[$collector->getName()] !== '0') {
        parent::add($collector);
      }
    }
  }

}
