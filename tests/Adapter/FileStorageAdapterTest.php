<?php

namespace Sweikenb\Library\Dirty\Tests\Adapter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Adapter\FileStorageAdapter;

#[CoversClass(FileStorageAdapter::class)]
class FileStorageAdapterTest extends TestCase
{
    public const STORAGE_DIR = '/tmp/sweikenb-dirty-testcase';

    public function setUp(): void
    {
        $this->adapter = new FileStorageAdapter(self::STORAGE_DIR);
    }

    public function tearDown(): void
    {
        if (file_exists(self::STORAGE_DIR)) {
            passthru(sprintf('rm -r %s', escapeshellarg(self::STORAGE_DIR)));
        }
    }

    private function getExpectedFileName(string $storageKey, string $type): string
    {
        return sprintf('%s/%s_%s.php', self::STORAGE_DIR, md5($storageKey), $type);
    }

    private function loadFileIsolated(string $filename): mixed
    {
        $this->assertFileExists($filename);

        return call_user_func(fn () => require $filename);
    }

    public function testSaveHash(): void
    {
        $storageKey = '123456789';
        $hashValue = '987654321';

        $filenameHash = $this->getExpectedFileName($storageKey, 'hash');
        $filenameData = $this->getExpectedFileName($storageKey, 'data');

        $this->assertFileDoesNotExist($filenameHash);
        $this->assertFileDoesNotExist($filenameData);

        $this->assertTrue($this->adapter->saveHash($storageKey, $hashValue));
        $this->assertSame($hashValue, $this->loadFileIsolated($filenameHash));
    }

    public function testSaveData(): void
    {
        $storageKey = '234567891';
        $data = [
            'some' => 'value',
            'another' => [
                'array' => 'type',
                'values' => 2,
            ],
        ];

        $filenameHash = $this->getExpectedFileName($storageKey, 'hash');
        $filenameData = $this->getExpectedFileName($storageKey, 'data');

        $this->assertFileDoesNotExist($filenameHash);
        $this->assertFileDoesNotExist($filenameData);

        $this->assertTrue($this->adapter->saveData($storageKey, $data));
        $this->assertEquals($data, $this->loadFileIsolated($filenameData));
    }

    public function testLoadHash(): void
    {
        $storageKey = '987654321';
        $hashValue = '876534567';

        $filenameHash = $this->getExpectedFileName($storageKey, 'hash');

        $this->assertTrue($this->adapter->saveHash($storageKey, $hashValue));
        $this->assertFileExists($filenameHash);
        $this->assertSame($hashValue, $this->adapter->loadHash($storageKey));
        unlink($filenameHash);
        $this->assertNull($this->adapter->loadHash($storageKey));
    }

    public function testLoadData(): void
    {
        $storageKey = '876534567';
        $data = [
            'some' => 'value',
            'another' => [
                'array' => 'type',
                'values' => 2,
            ],
        ];

        $filenameData = $this->getExpectedFileName($storageKey, 'data');

        $this->assertTrue($this->adapter->saveData($storageKey, $data));
        $this->assertFileExists($filenameData);
        $this->assertEquals($data, $this->adapter->loadData($storageKey));
        unlink($filenameData);
        $this->assertNull($this->adapter->loadData($storageKey));
    }
}
