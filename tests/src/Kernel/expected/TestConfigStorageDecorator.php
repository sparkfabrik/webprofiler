<?php

namespace Drupal\webprofiler\Entity;

use Drupal\webprofiler\Entity\ConfigEntityStorageDecorator;
/**
 * This file is auto-generated.
 */
class TestConfigStorageDecorator extends ConfigEntityStorageDecorator implements \Drupal\webprofiler_config_entity\TestConfigStorageInterface
{
    public function method0()
    {
        return $this->getOriginalObject()->method0();
    }
    public function method1(): void
    {
        $this->getOriginalObject()->method1();
    }
    public function method2($param1): int
    {
        return $this->getOriginalObject()->method2($param1);
    }
    public function method3(string $param1): string
    {
        return $this->getOriginalObject()->method3($param1);
    }
    public function method4(string $param1, string $param2): string|null
    {
        return $this->getOriginalObject()->method4($param1, $param2);
    }
    public function method5(string $param1, string $param2 = NULL): ?string
    {
        return $this->getOriginalObject()->method5($param1, $param2);
    }
    public function method6(string $param1, bool $param2 = FALSE): mixed
    {
        return $this->getOriginalObject()->method6($param1, $param2);
    }
    public function method7(string $param1, bool $param2 = TRUE): void
    {
        $this->getOriginalObject()->method7($param1, $param2);
    }
    public function method8(string $param1, array $param2 = []): void
    {
        $this->getOriginalObject()->method8($param1, $param2);
    }
    public function method9(string $param1, int $param2 = 5): void
    {
        $this->getOriginalObject()->method9($param1, $param2);
    }
    public function method10(string $param1): void
    {
        $this->getOriginalObject()->method10($param1);
    }
}
