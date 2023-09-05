<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Twig\Extension;

use Drupal\sdc\ComponentPluginManager;
use Drupal\webprofiler\Twig\ComponentNodeVisitor;
use Twig\Extension\AbstractExtension;

/**
 * Twig extension relate to PHP code and used by Webprofiler.
 */
class ComponentExtension extends AbstractExtension {

  /**
   * ComponentExtension constructor.
   *
   * @param \Drupal\sdc\ComponentPluginManager $pluginManager
   *   The component plugin manager.
   */
  public function __construct(
    private readonly ComponentPluginManager $pluginManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getNodeVisitors(): array {
    /** @var \Drupal\webprofiler\DataCollector\ThemeDataCollector $dataCollector */
    $dataCollector = \Drupal::service('webprofiler.theme');

    return [new ComponentNodeVisitor($this->pluginManager, $dataCollector)];
  }

}
