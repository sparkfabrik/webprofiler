<?php

namespace Drupal\webprofiler\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

interface TemplateAwareDataCollectorInterface extends DataCollectorInterface
{
    public static function getTemplate(): ?string;
}
