<?php

namespace Drupal\webprofiler_config_entity;

/**
 * Defines the testconfig storage.
 */
interface TestConfigStorageInterface {

  public function method0();

  public function method1(): void;

  public function method2($param1): int;

  public function method3(string $param1): string;

  public function method4(string $param1, string $param2): string|null;

  public function method5(string $param1, string $param2 = NULL): ?string;

  public function method6(string $param1, bool $param2 = FALSE): mixed;

  public function method7(string $param1, bool $param2 = TRUE): void;

  public function method8(string $param1, array $param2 = []): void;

  public function method9(string $param1, int $param2 = 5): void;

  public function method10(string $param1): void;

}
