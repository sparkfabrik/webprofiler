<?php

namespace Drupal\webprofiler;

use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\SpanExporter\ConsoleSpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class Tracer {

  function getTracer(): TracerInterface {
    $tracerProvider =  new TracerProvider(
      new SimpleSpanProcessor(
        new ConsoleSpanExporter()
      )
    );

    return $tracerProvider->getTracer('io.opentelemetry.contrib.php');
  }

}
