<?php

namespace Drupal\webprofiler\DataCollector;

use Drupal\webprofiler\Http\HttpClientMiddleware;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Collects data about http calls during request.
 */
class HttpDataCollector extends DataCollector implements HasPanelInterface {

  use StringTranslationTrait;

  /**
   * @param \Drupal\webprofiler\Http\HttpClientMiddleware $middleware
   */
  public function __construct(private readonly HttpClientMiddleware $middleware) {
    $this->data['completed'] = [];
    $this->data['failed'] = [];
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return 'http';
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
    $completed = $this->middleware->getCompletedRequests();
    $failed = $this->middleware->getFailedRequests();

    foreach ($completed as $data) {
      /** @var \GuzzleHttp\Psr7\Request $request */
      $request = $data['request'];
      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $data['response'];
      /** @var \GuzzleHttp\TransferStats $stats */
      $stats = $request->stats;

      $uri = $request->getUri();
      $this->data['completed'][] = [
        'request' => [
          'method' => $request->getMethod(),
          'uri' => [
            'schema' => $uri->getScheme(),
            'host' => $uri->getHost(),
            'port' => $uri->getPort(),
            'path' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'fragment' => $uri->getFragment(),
          ],
          'headers' => $request->getHeaders(),
          'protocol' => $request->getProtocolVersion(),
          'request_target' => $request->getRequestTarget(),
          'stats' => [
            'transferTime' => $stats->getTransferTime(),
            'handlerStats' => $stats->getHandlerStats(),
          ],
        ],
        'response' => [
          'phrase' => $response->getReasonPhrase(),
          'status' => $response->getStatusCode(),
          'headers' => $response->getHeaders(),
          'protocol' => $response->getProtocolVersion(),
        ],
      ];
    }

    foreach ($failed as $data) {
      /** @var \GuzzleHttp\Psr7\Request $request */
      $request = $data['request'];
      /** @var \GuzzleHttp\Psr7\Response $response */
      $response = $data['response'];

      $uri = $request->getUri();
      $failureData = [
        'request' => [
          'method' => $request->getMethod(),
          'uri' => [
            'schema' => $uri->getScheme(),
            'host' => $uri->getHost(),
            'port' => $uri->getPort(),
            'path' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'fragment' => $uri->getFragment(),
          ],
          'headers' => $request->getHeaders(),
          'protocol' => $request->getProtocolVersion(),
          'request_target' => $request->getRequestTarget(),
        ],
      ];

      if ($response) {
        $failureData['response'] = [
          'phrase' => $response->getReasonPhrase(),
          'status' => $response->getStatusCode(),
          'headers' => $response->getHeaders(),
          'protocol' => $response->getProtocolVersion(),
        ];
      }

      $this->data['failed'][] = $failureData;
    }
  }

  /**
   * @return int
   */
  public function getCompletedRequestsCount(): int {
    return count($this->getCompletedRequests());
  }

  /**
   * @return array
   */
  public function getCompletedRequests(): array {
    return $this->data['completed'];
  }

  /**
   * @return int
   */
  public function getFailedRequestsCount(): int {
    return count($this->getFailedRequests());
  }

  /**
   * @return array
   */
  public function getFailedRequests(): array {
    return $this->data['failed'];
  }

  /**
   * {@inheritdoc}
   */
  public function getPanel(): array {
    $build = [];

    return $build;
  }
}
