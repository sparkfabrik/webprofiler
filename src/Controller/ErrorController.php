<?php

namespace Drupal\webprofiler\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Renders error or exception pages from a given FlattenException.
 */
class ErrorController {

  /**
   * Invokes the controller.
   *
   * @param \Throwable $exception
   *   The exception to render.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function __invoke(\Throwable $exception): Response {
    $exception = \Drupal::service('webprofiler.error_renderer')
      ->render($exception);

    return new Response($exception->getAsString(), $exception->getStatusCode(), $exception->getHeaders());
  }

}
