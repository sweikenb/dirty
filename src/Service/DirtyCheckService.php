<?php

namespace Sweikenb\Library\Dirty\Service;

use Sweikenb\Library\Dirty\Api\ConfigInterface;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\DirtyCheckResultModel;

class DirtyCheckService
{
    private readonly Factory $factory;
    private readonly NormalizerService $normalizer;
    private readonly StorageService $storage;
    private readonly ModelDiffService $modelDiff;

    public function __construct(
        private readonly bool $checkHashBeforeLoad = true,
        ?Factory $factory = null,
        ?NormalizerService $normalizer = null,
        ?StorageService $storage = null,
        ?ModelDiffService $modelDiff = null
    ) {
        $this->factory = $factory ?? new Factory();
        $this->normalizer = $normalizer ?? new NormalizerService();
        $this->storage = $storage ?? new StorageService();
        $this->modelDiff = $modelDiff ?? new ModelDiffService();
    }

    public function execute(string $id, array|object $toCheck, ?ConfigInterface $config = null): DirtyCheckResultModel
    {
        $current = $this->normalizer->execute($id, $toCheck, $config);

        $diff = [];
        if (!$this->checkHashBeforeLoad || $this->storage->hasChanges($current)) {
            $diff = $this->modelDiff->execute($this->storage->getPreviousNormalization($current), $current);
            $this->storage->store($current);
        }

        return $this->factory->createResult($diff);
    }
}
