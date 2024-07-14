<?php

namespace Sweikenb\Library\Dirty\Service;

use Sweikenb\Library\Dirty\Adapter\FileStorageAdapter;
use Sweikenb\Library\Dirty\Api\StorageAdapterInterface;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\NormalizedModel;

class StorageService
{
    private readonly Factory $factory;
    private readonly StorageAdapterInterface $adapter;

    public function __construct(?Factory $factory = null, ?StorageAdapterInterface $adapter = null)
    {
        $this->factory = $factory ?? new Factory();
        $this->adapter = $adapter ?? new FileStorageAdapter();
    }

    public function getPreviousNormalization(NormalizedModel $current): ?NormalizedModel
    {
        if ($data = $this->adapter->loadData($current->storageKey)) {
            return $this->factory->createNormalized(
                $current->storageKey,
                $data['fieldPaths'] ?? [],
                $data['hash'] ?? []
            );
        }

        return null;
    }

    public function store(NormalizedModel $model): bool
    {
        return $this->adapter->saveHash($model->storageKey, $model->hash)
            && $this->adapter->saveData($model->storageKey, [
                'fieldPaths' => $model->fieldPaths,
                'hash' => $model->hash,
            ]);
    }

    public function hasChanges(NormalizedModel $current): bool
    {
        return $current->hash !== $this->adapter->loadHash($current->storageKey);
    }
}
