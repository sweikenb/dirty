<?php

namespace Sweikenb\Library\Dirty\Api;

interface ConfigInterface
{
    public function setFieldsToCheck(string ...$fieldPath): void;

    /**
     * @return string[]
     */
    public function getFieldsToCheck(): array;

    public function setFieldsToIgnore(string ...$fieldPath): void;

    /**
     * @return string[]
     */
    public function getFieldsToIgnore(): array;
}
