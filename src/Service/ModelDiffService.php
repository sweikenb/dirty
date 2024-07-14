<?php

namespace Sweikenb\Library\Dirty\Service;

use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\NormalizedModel;

class ModelDiffService
{
    private readonly Factory $factory;

    public function __construct(?Factory $factory = null)
    {
        $this->factory = $factory ?? new Factory();
    }

    public function execute(?NormalizedModel $previous, NormalizedModel $current): array
    {
        $diff = [];
        foreach ($previous?->fieldPaths ?? [] as $fieldPath => $value) {
            if (!isset($current->fieldPaths[$fieldPath]) || $value !== $current->fieldPaths[$fieldPath]) {
                $diff[$fieldPath] = $this->factory->createValueDiff(
                    $fieldPath,
                    $value,
                    $current->fieldPaths[$fieldPath] ?? null
                );
            }
        }
        foreach ($current->fieldPaths as $fieldPath => $value) {
            if (!isset($previous?->fieldPaths[$fieldPath]) || $value !== $previous->fieldPaths[$fieldPath]) {
                $diff[$fieldPath] = $this->factory->createValueDiff(
                    $fieldPath,
                    $previous?->fieldPaths[$fieldPath] ?? null,
                    $value
                );
            }
        }
        ksort($diff);

        return $diff;
    }
}
