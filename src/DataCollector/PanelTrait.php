<?php

declare(strict_types=1);

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\webprofiler\MethodData;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Base class for dashboard panels.
 */
trait PanelTrait {

  use StringTranslationTrait;

  /**
   * Internal resource to store dumped data.
   *
   * @var resource
   */
  private $output;

  /**
   * Dump data using a dumper.
   *
   * @param \Symfony\Component\VarDumper\Cloner\Data $data
   *   The data to dump.
   * @param int $maxDepth
   *   The max depth to dump for complex data.
   *
   * @return string|string[]
   *   The string representation of dumped data.
   */
  public function dumpData(Data $data, int $maxDepth = 0): array|string {
    $dumper = new HtmlDumper();
    $dumper->setOutput($this->output = fopen('php://memory', 'r+b'));
    $dumper->setTheme('light');

    $file_link_formatter = \Drupal::service('webprofiler.file_link_formatter');
    $dumper->setDisplayOptions(['fileLinkFormat' => $file_link_formatter]);

    $dumper->dump($data, NULL, [
      'maxDepth' => $maxDepth,
    ]);

    $dump = stream_get_contents($this->output, -1, 0);
    rewind($this->output);
    ftruncate($this->output, 0);

    return str_replace("\n</pre", '</pre', rtrim($dump));
  }

  /**
   * Render data in an array as HTML table.
   *
   * @param array $data
   *   The data to render.
   * @param string $label
   *   The table label.
   * @param callable|null $element_converter
   *   An optional function to convert all elements of data before rendering.
   *   If NULL fallback to PanelBase::dumpData.
   *
   * @return array
   *   A render array.
   */
  protected function renderTable(
    array $data,
    string $label,
    callable $element_converter = NULL
  ): array {
    if (count($data) == 0) {
      return [];
    }

    if ($element_converter == NULL) {
      $element_converter = [$this, 'dumpData'];
    }

    $rows = [];
    foreach ($data as $key => $el) {
      $rows[] = [
        [
          'data' => $key,
          'class' => 'webprofiler__key',
        ],
        [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{{ data|raw }}',
            '#context' => [
              'data' => $element_converter($el),
            ],
          ],
          'class' => 'webprofiler__value',
        ],
      ];
    }

    return [
      $label => [
        '#theme' => 'webprofiler_dashboard_table',
        '#title' => $label,
        '#data' => [
          '#type' => 'table',
          '#header' => [$this->t('Name'), $this->t('Value')],
          '#rows' => $rows,
          '#attributes' => [
            'class' => [
              'webprofiler__table',
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Render a link to a file.
   *
   * @param string|false $file
   *   The file path.
   * @param int $line
   *   The file line.
   * @param string $label
   *   A label to display.
   *
   * @return array
   *   A render array for the link.
   */
  protected function renderClasslink(string|false $file, int $line, string $label): array {
    $flf = \Drupal::service('webprofiler.file_link_formatter');

    return [
      '#type' => 'inline_template',
      '#template' => '<a href="{{ href }}">{{ label }}</a>',
      '#context' => [
        'href' => $flf->format($file ?: '', $line),
        'label' => $label,
      ],
    ];
  }

  /**
   * Render a link to a file from a MethodData object.
   *
   * @param \Drupal\webprofiler\MethodData $method
   *   MethodData object.
   *
   * @return array
   *   A render array for the link.
   */
  protected function renderClassLinkFromMethodData(MethodData $method): array {
    return $this->renderClasslink($method->getFile(), $method->getLine(), $method->getClass() . '::' . $method->getMethod());
  }

  /**
   * Render a time value.
   *
   * @param float $time
   *   The time value.
   * @param string $unit
   *   The time unit.
   *
   * @return string
   *   The rendered time value.
   */
  protected function renderTime(float $time, string $unit = 'ms'): string {
    $time = round($time * 100, 2) / 100;

    return $time . ' ' . $unit;
  }

}
