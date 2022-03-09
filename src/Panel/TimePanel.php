<?php

namespace Drupal\webprofiler\Panel;

/**
 * Panel to render collected data about time.
 */
class TimePanel extends PanelBase implements PanelInterface {

  /**
   * {@inheritDoc}
   */
  public function render($token, $name): array {
    /** @var \Symfony\Component\HttpKernel\Profiler\Profiler $profiler */
    $profiler = \Drupal::service('webprofiler.profiler');
    /** @var \Drupal\webprofiler\DataCollector\BlocksDataCollector $collector */
    $collector = $profiler->loadProfile($token)->getCollector($name);

    $data = [];
    return [
      '#theme' => 'webprofiler_dashboard_panel',
      '#title' => $this->t('Blocks'),
      '#data' => $data,
    ];
  }

}
