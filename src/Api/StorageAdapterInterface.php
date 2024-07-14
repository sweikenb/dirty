<?php

namespace Sweikenb\Library\Dirty\Api;

interface StorageAdapterInterface
{
    public function saveHash(string $storageKey, string $hash): bool;

    public function saveData(string $storageKey, array $data): bool;

    public function loadHash(string $storageKey): ?string;

    public function loadData(string $storageKey): ?array;
}
