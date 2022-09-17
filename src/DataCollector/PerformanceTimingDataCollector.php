<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects data about frontend performance.
 */
class PerformanceTimingDataCollector extends DataCollector implements HasPanelInterface {

  use StringTranslationTrait, DataCollectorTrait, PanelTrait;

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'performance_timing';
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * Set performance data.
   *
   * @param array $data
   *   The performance data.
   */
  public function setData(array $data) {
    $this->data['performance'] = $data;
  }

  /**
   * {@inheritdoc}
   */
  public function getPanel(): array {
    return [
      '#type' => 'inline_template',
      '#template' => '{{ data|raw }}',
      '#context' => [
        'data' => $this->dumpData($this->cloneVar($this->data['performance'])),
      ],
    ];
  }

}
