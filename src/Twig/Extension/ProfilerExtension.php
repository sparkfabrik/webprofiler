<?php

declare(strict_types=1);

namespace Drupal\webprofiler\Twig\Extension;

use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Extension\ProfilerExtension as TwigProfilerExtension;
use Twig\Profiler\Profile;

/**
 * Profile Twig templates.
 */
class ProfilerExtension extends TwigProfilerExtension {

  /**
   * @var \Symfony\Component\Stopwatch\Stopwatch
   */
  private Stopwatch $stopwatch;
  private \SplObjectStorage $events;

  /**
   * ProfilerExtension constructor.
   */
  public function __construct(Profile $profile, Stopwatch $stopwatch) {
    parent::__construct($profile);

    $this->stopwatch = $stopwatch;
    $this->events = new \SplObjectStorage();
  }

  /**
   * {@inheritdoc}
   */
  public function enter(Profile $profile) {
    if ($profile->isTemplate()) {
      $this->events[$profile] = $this->stopwatch->start($profile->getName(), 'template');
    }

    parent::enter($profile);
  }

  /**
   * {@inheritdoc}
   */
  public function leave(Profile $profile) {
    parent::leave($profile);

    if ($profile->isTemplate()) {
      $this->events[$profile]->stop();
      unset($this->events[$profile]);
    }
  }
}
