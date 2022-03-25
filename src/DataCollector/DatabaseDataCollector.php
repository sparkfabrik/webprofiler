<?php

declare(strict_types=1);

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 *
 */
class DatabaseDataCollector extends DataCollector implements HasPanelInterface {

  /**
   * DatabaseDataCollector constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(
    protected readonly Connection $database,
    protected readonly ConfigFactoryInterface $configFactory
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Throwable $exception = NULL) {
    $connections = [];
    foreach (Database::getAllConnectionInfo() as $key => $info) {
      try {
        $database = Database::getConnection('default', $key);

        if ($database->getLogger()) {
          $connections[$key] = $database->getLogger()->get('webprofiler');
        }
      } catch(\Exception $e) {
        // There was some error during database connection, maybe a stale
        // configuration in settings.php or wrong values used for a migration.
      }
    }

    $this->data['connections'] = array_keys($connections);

    $data = [];
    foreach ($connections as $key => $queries) {
      foreach ($queries as $query) {
        // Remove caller args.
        unset($query['caller']['args']);

        // Remove query args element if empty.
        if (isset($query['args']) && empty($query['args'])) {
          unset($query['args']);
        }

        // Save time in milliseconds.
        $query['time'] = $query['time'] * 1000;
        $query['database'] = $key;
        $data[] = $query;
      }
    }

    $querySort = $this
      ->configFactory
      ->get('webprofiler.config')
      ->get('query_sort');

    if ('duration' === $querySort) {
      usort(
        $data, [
          "Drupal\\webprofiler\\DataCollector\\DatabaseDataCollector",
          "orderQueryByTime",
        ]
      );
    }

    $this->data['queries'] = $data;

    $options = $this->database->getConnectionOptions();

    // Remove password for security.
    unset($options['password']);

    $this->data['database'] = $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'database';
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
  public function getPanel(): array {
    // Panel is implemented in the template.
    return [];
  }

  /**
   * @return array
   */
  public function getDatabase() {
    return $this->data['database'];
  }

  /**
   * @return int
   */
  public function getQueryCount() {
    return count($this->data['queries']);
  }

  /**
   * @return array
   */
  public function getQueries() {
    return $this->data['queries'];
  }

  /**
   * Returns the total execution time.
   *
   * @return float
   */
  public function getTime() {
    $time = 0;

    foreach ($this->data['queries'] as $query) {
      $time += $query['time'];
    }

    return $time;
  }

  /**
   * Returns the configured query highlight threshold.
   *
   * @return int
   */
  public function getQueryHighlightThreshold() {
    // When a profile is loaded from storage this object is deserialized and
    // no constructor is called so we cannot use dependency injection.
    return \Drupal::config('webprofiler.config')->get('query_highlight');
  }

  /**
   * @param $a
   * @param $b
   *
   * @return int
   */
  private function orderQueryByTime($a, $b) {
    $at = $a['time'];
    $bt = $b['time'];

    if ($at == $bt) {
      return 0;
    }
    return ($at < $bt) ? 1 : -1;
  }

}
