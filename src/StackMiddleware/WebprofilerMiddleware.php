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
   * Constructs a WebprofilerMiddleware object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel
   *   The decorated kernel.
   */
  public function __construct(protected readonly HttpKernelInterface $httpKernel) {
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response {
    foreach (Database::getAllConnectionInfo() as $key => $info) {
      Database::startLog('webprofiler', $key);
    }

    return $this->httpKernel->handle($request, $type, $catch);
  }

}
