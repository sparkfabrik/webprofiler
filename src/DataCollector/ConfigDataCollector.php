<?php

declare(strict_types=1);

namespace Drupal\webprofiler\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * Collects config data.
 */
class ConfigDataCollector extends DataCollector implements LateDataCollectorInterface {

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $this->data = [
      'token' => $response->headers->get('X-Debug-Token'),
      'drupal_version' => \Drupal::VERSION,
      'php_version' => \PHP_VERSION,
      'php_architecture' => \PHP_INT_SIZE * 8,
      'php_timezone' => date_default_timezone_get(),
      'xdebug_enabled' => \extension_loaded('xdebug'),
      'apcu_enabled' => \extension_loaded('apcu') && filter_var(ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN),
      'zend_opcache_enabled' => \extension_loaded('Zend OPcache') && filter_var(ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN),
      'bundles' => [],
      'sapi_name' => \PHP_SAPI,
    ];

    if (preg_match('~^(\d+(?:\.\d+)*)(.+)?$~', $this->data['php_version'], $matches) && isset($matches[2])) {
      $this->data['php_version'] = $matches[1];
      $this->data['php_version_extra'] = $matches[2];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'config';
  }

  /**
   * {@inheritdoc}
   */
  public function lateCollect() {
    $this->data = $this->cloneVar($this->data);
  }

  /**
   * Reset the collected data.
   */
  public function reset() {
    $this->data = [];
  }

  /**
   * Gets the token.
   */
  public function getToken(): ?string {
    return $this->data['token'];
  }

  /**
   * Gets the Symfony version.
   */
  public function getDrupalVersion(): string {
    return $this->data['drupal_version'];
  }

  /**
   * Gets the PHP version.
   */
  public function getPhpVersion(): string {
    return $this->data['php_version'];
  }

  /**
   * Gets the PHP version extra part.
   */
  public function getPhpVersionExtra(): ?string {
    return $this->data['php_version_extra'] ?? NULL;
  }

  /**
   * Gets the PHP architecture.
   */
  public function getPhpArchitecture(): int {
    return $this->data['php_architecture'];
  }

  /**
   * Gets the PHP timezone.
   */
  public function getPhpTimezone(): string {
    return $this->data['php_timezone'];
  }

  /**
   * Returns true if the XDebug is enabled.
   */
  public function hasXdebug(): bool {
    return $this->data['xdebug_enabled'];
  }

  /**
   * Returns true if APCu is enabled.
   */
  public function hasApcu(): bool {
    return $this->data['apcu_enabled'];
  }

  /**
   * Returns true if Zend OPcache is enabled.
   */
  public function hasZendOpcache(): bool {
    return $this->data['zend_opcache_enabled'];
  }

  /**
   * Gets the PHP SAPI name.
   */
  public function getSapiName(): string {
    return $this->data['sapi_name'];
  }

}
