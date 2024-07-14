<?php

namespace Sweikenb\Library\Dirty\Model;

use Sweikenb\Library\Dirty\Api\ConfigInterface;

class ConfigModel implements ConfigInterface
{
    public function __construct(
        private array $checkFieldPath = [],
        private array $ignoreFieldPath = []
    ) {
    }

    public function setFieldsToCheck(string ...$fieldPath): void
    {
        $this->checkFieldPath = $fieldPath;
    }

    public function getFieldsToCheck(): array
    {
        return $this->checkFieldPath;
    }

    public function setFieldsToIgnore(string ...$fieldPath): void
    {
        $this->ignoreFieldPath = $fieldPath;
    }

    public function getFieldsToIgnore(): array
    {
        return $this->ignoreFieldPath;
    }
}
