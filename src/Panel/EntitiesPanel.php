<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Panel;

/**
 * Panel to render collected data about entities.
 */
class EntitiesPanel extends PanelBase implements PanelInterface {

  /**
   * {@inheritDoc}
   */
  public function render($token, $name): array {
    $data = [];

    return [
      '#theme' => 'webprofiler_dashboard_panel',
      '#title' => $this->t('Entities'),
      '#data' => $data,
    ];
  }

}
