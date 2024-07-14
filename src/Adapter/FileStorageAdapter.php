<?php

namespace Sweikenb\Library\Dirty\Adapter;

use Sweikenb\Library\Dirty\Api\StorageAdapterInterface;

class FileStorageAdapter implements StorageAdapterInterface
{
    public const DEFAULT_STORAGE_DIR = '/tmp/sweikenb/dirty';
    public const DEFAULT_PERMISSIONS = 0775;
    private string $storageDir;

    public function __construct(?string $storageDir = null, ?int $permissions = null)
    {
        $this->storageDir = $storageDir ?? self::DEFAULT_STORAGE_DIR;
        is_dir($this->storageDir) || mkdir($this->storageDir, $permissions ?? self::DEFAULT_PERMISSIONS, true);
    }

    public function saveHash(string $storageKey, string $hash): bool
    {
        $content = sprintf('<?php return %s;', var_export($hash, true));

        return file_put_contents($this->getFilename($storageKey, 'hash'), $content) > 0;
    }

    public function saveData(string $storageKey, array $data): bool
    {
        $content = sprintf('<?php return %s;', var_export($data, true));

        return file_put_contents($this->getFilename($storageKey, 'data'), $content) > 0;
    }

    public function loadHash(string $storageKey): ?string
    {
        $filename = $this->getFilename($storageKey, 'hash');
        if (file_exists($filename)) {
            return call_user_func(fn () => require $filename);
        }

        return null;
    }

    public function loadData(string $storageKey): ?array
    {
        $filename = $this->getFilename($storageKey, 'data');
        if (file_exists($filename)) {
            return call_user_func(fn () => require $filename);
        }

        return null;
    }

    private function getFilename(string $storageKey, string $type): string
    {
        return sprintf('%s/%s_%s.php', $this->storageDir, md5($storageKey), $type);
    }
}
