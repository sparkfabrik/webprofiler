<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Panel;

/**
 * Interface for dashboard panels.
 */
interface PanelInterface {

  /**
   * Render a panel.
   *
   * @param string $token
   *   A profile token.
   * @param string $name
   *   The panel name.
   *
   * @return array
   *   A render array for this panel.
   */
  public function render(string $token, string $name): array;

}
