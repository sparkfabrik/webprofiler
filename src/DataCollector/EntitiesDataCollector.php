<?php

declare(strict_types=1);

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webprofiler\Entity\EntityDecorator;
use Drupal\webprofiler\Panel\BlocksPanel;
use Drupal\webprofiler\Panel\EntitiesPanel;
use Drupal\webprofiler\Panel\PanelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects entities data.
 */
class EntitiesDataCollector extends DataCollector implements HasPanelInterface {

  /**
   * EntitiesDataCollector constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   *   The Entity type manager service.
   */
  public function __construct(protected readonly EntityTypeManagerInterface $entityManager) {
    $this->data['entities']['loaded'] = [];
    $this->data['entities']['rendered'] = [];
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $storage = $this->entityManager->getStorage('block');

    $loaded = $this->entityManager->getLoadedByKind('content');
    //$rendered = $this->entityManager->getRendered('block');

    if ($loaded) {
//      $this->data['blocks']['loaded'] = $this->getEntityData($loaded, $storage);
    }

//    if ($rendered) {
//      $this->data['blocks']['rendered'] = $this->getBlocksData($rendered, $storage);
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'entities';
  }

  /**
   * {@inheritdoc}
   */
  public function getPanel(): PanelInterface {
    return new EntitiesPanel();
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * @return int
   */
  public function getLoadedEntitiesCount(): int {
    return 5;
  }

}
