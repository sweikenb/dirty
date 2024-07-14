<?php

namespace Sweikenb\Library\Dirty;

use Sweikenb\Library\Dirty\Api\ConfigInterface;
use Sweikenb\Library\Dirty\Model\ConfigModel;
use Sweikenb\Library\Dirty\Model\DirtyCheckResultModel;
use Sweikenb\Library\Dirty\Model\NormalizedModel;
use Sweikenb\Library\Dirty\Model\DiffModel;

class Factory
{
    /**
     * @param array<string, DiffModel> $diffFieldPaths
     */
    public function createResult(array $diffFieldPaths): DirtyCheckResultModel
    {
        return new DirtyCheckResultModel(!empty($diffFieldPaths), $diffFieldPaths);
    }

    public function createNormalized(string $storageKey, array $fieldPaths, string $hash): NormalizedModel
    {
        return new NormalizedModel($storageKey, $fieldPaths, $hash);
    }

    public function createConfig(array $checkFieldPaths, array $ignoreFieldPaths): ConfigInterface
    {
        return new ConfigModel($checkFieldPaths, $ignoreFieldPaths);
    }

    public function createValueDiff(string $fieldPath, mixed $previously, mixed $currently): DiffModel
    {
        return new DiffModel($fieldPath, $previously, $currently);
    }
}
