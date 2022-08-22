<?php

namespace Drupal\webprofiler\Http;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;

/**
 * Class HttpClientMiddleware
 */
class HttpClientMiddleware {

  /**
   * @var array
   */
  private array $completedRequests;

  /**
   * @var array
   */
  private array $failedRequests;

  /**
   *
   */
  public function __construct() {
    $this->completedRequests = [];
    $this->failedRequests = [];
  }

  /**
   *
   *
   */
  public function __invoke(): \Closure {
    return function ($handler) {
      return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {

        // If on_stats callback is already set then save it
        // and call it after ours.
        $next = $options['on_stats'] ?? function (TransferStats $stats) {
        };

        $options['on_stats'] = function (TransferStats $stats) use ($request, $next) {
          $request->stats = $stats;
          $next($stats);
        };

        return $handler($request, $options)->then(
          function ($response) use ($request) {

            $this->completedRequests[] = [
              'request' => $request,
              'response' => $response,
            ];

            return $response;
          },
          function ($reason) use ($request) {
            $response = $reason instanceof RequestException
              ? $reason->getResponse()
              : NULL;

            $this->failedRequests[] = [
              'request' => $request,
              'response' => $response,
              'message' => $reason->getMessage(),
            ];

            return Create::rejectionFor($reason);
          }
        );
      };
    };
  }

  /**
   * @return array
   */
  public function getCompletedRequests(): array {
    return $this->completedRequests;
  }

  /**
   * @return array
   */
  public function getFailedRequests(): array {
    return $this->failedRequests;
  }

}
