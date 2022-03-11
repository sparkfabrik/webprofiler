<?php

namespace Drupal\webprofiler\StackMiddleware;

use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class WebprofilerMiddleware
 */
class WebprofilerMiddleware implements HttpKernelInterface {

  /**
   * The decorated kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected HttpKernelInterface $httpKernel;

  /**
   * Constructs a WebprofilerMiddleware object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The decorated kernel.
   */
  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response {
    foreach (Database::getAllConnectionInfo() as $key => $info) {
      Database::startLog('webprofiler', $key);
    }

    $tracer_service = \Drupal::service('webprofiler.tracer');
    $tracer = $tracer_service->getTracer();

    $rootSpan = $tracer->spanBuilder('root')->startSpan();
    $rootSpan->activate();

    $response = $this->httpKernel->handle($request, $type, $catch);

    $rootSpan->end();

    return $response;
  }

}
