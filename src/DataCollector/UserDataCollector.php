<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\Authentication\AuthenticationCollectorInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class UserDataCollector
 */
class UserDataCollector extends DataCollector {

  use StringTranslationTrait;

  /**
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityManager
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Authentication\AuthenticationCollectorInterface $providerCollector
   */
  public function __construct(
    private readonly AccountInterface $currentUser,
    private readonly EntityTypeManagerInterface $entityManager,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly AuthenticationCollectorInterface $providerCollector
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'user';
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $this->data['name'] = $this->currentUser->getDisplayName();
    $this->data['authenticated'] = $this->currentUser->isAuthenticated();

    $this->data['roles'] = [];
    $storage = $this->entityManager->getStorage('user_role');
    foreach ($this->currentUser->getRoles() as $role) {
      $entity = $storage->load($role);
      if ($entity) {
        $this->data['roles'][] = $entity->label();
      }
    }

    foreach ($this->providerCollector->getSortedProviders() as $provider_id => $provider) {
      if ($provider->applies($request)) {
        $this->data['provider'] = $provider_id;
      }
    }

    $this->data['anonymous'] = $this->configFactory->get('user.settings')
      ->get('anonymous');
  }

  /**
   * @return string
   */
  public function getUserName(): string {
    return $this->data['name'];
  }

  /**
   * @return bool
   */
  public function getAuthenticated(): bool {
    return $this->data['authenticated'];
  }

  /**
   * @return array
   */
  public function getRoles(): array {
    return $this->data['roles'];
  }

  /**
   * @return string
   */
  public function getProvider(): string {
    return $this->data['provider'];
  }

  /**
   * @return string
   */
  public function getAnonymous(): string {
    return $this->data['anonymous'];
  }

}
