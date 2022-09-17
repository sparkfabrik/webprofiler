<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\webprofiler\Profiler\Profiler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Profiler\Profile;

/**
 * Collects frontend performance data.
 */
class PerformanceTimingController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): PerformanceTimingController {
    return new static(
      $container->get('webprofiler.profiler')
    );
  }

  /**
   * PerformanceTimingController constructor.
   *
   * @param \Drupal\webprofiler\Profiler\Profiler $profiler
   *   The profiler.
   */
  final public function __construct(private readonly Profiler $profiler) {
  }

  /**
   * Save the performance data to performance_timing collector.
   *
   * @param \Symfony\Component\HttpKernel\Profiler\Profile $profile
   *   The profile.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *  The response.
   */
  public function savePerformanceTimingAction(Profile $profile, Request $request): JsonResponse {
    $this->profiler->disable();

    $data = Json::decode($request->getContent());

    /** @var \Drupal\webprofiler\DataCollector\PerformanceTimingDataCollector $collector */
    $collector = $profile->getCollector('performance_timing');
    $collector->setData($data);
    $this->profiler->updateProfile($profile);

    return new JsonResponse(['success' => TRUE]);
  }
}
