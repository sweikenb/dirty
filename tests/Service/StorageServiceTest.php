<?php

namespace Sweikenb\Library\Dirty\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Api\StorageAdapterInterface;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\NormalizedModel;
use Sweikenb\Library\Dirty\Service\StorageService;

#[CoversClass(StorageService::class)]
class StorageServiceTest extends TestCase
{
    private Factory|MockObject|null $factory = null;
    private StorageAdapterInterface|MockObject|null $adapter = null;

    public function setUp(): void
    {
        $this->factory = $this->createMock(Factory::class);
        $this->adapter = $this->createMock(StorageAdapterInterface::class);
    }

    public function testGetPreviousNormalization(): void
    {
        $data = ['fieldPaths' => ['field' => 'value'], 'hash' => '123'];

        $modelMock = $this->getMockBuilder(NormalizedModel::class)
            ->setConstructorArgs(
                [
                    'storage_key',
                    $data['fieldPaths'],
                    $data['hash'],
                ]
            )
            ->getMock();

        $this->adapter
            ->expects($this->exactly(2))
            ->method('loadData')
            ->willReturnOnConsecutiveCalls(
                null,
                $data
            );

        $this->factory
            ->expects($this->once())
            ->method('createNormalized')
            ->with(
                $this->equalTo('storage_key'),
                $this->equalTo($data['fieldPaths']),
                $this->equalTo($data['hash']),
            )
            ->willReturn($modelMock);

        $service = new StorageService($this->factory, $this->adapter);
        $this->assertNull($service->getPreviousNormalization($modelMock));
        $this->assertNotNull($service->getPreviousNormalization($modelMock));
    }

    public function testStore(): void
    {
        $data = ['fieldPaths' => ['field' => 'value'], 'hash' => '123'];

        $modelMock = $this->getMockBuilder(NormalizedModel::class)
            ->setConstructorArgs(
                [
                    'storage_key',
                    $data['fieldPaths'],
                    $data['hash'],
                ]
            )
            ->getMock();

        $this->adapter
            ->expects($this->exactly(3))
            ->method('saveHash')
            ->with(
                $this->equalTo('storage_key'),
                $this->equalTo($data['hash']),
            )
            ->willReturnOnConsecutiveCalls(true, true, false);

        $this->adapter
            ->expects($this->exactly(2))
            ->method('saveData')
            ->with(
                $this->equalTo('storage_key'),
                $this->equalTo($data),
            )
            ->willReturn(true, false);

        $service = new StorageService($this->factory, $this->adapter);
        $this->assertTrue($service->store($modelMock));
        $this->assertFalse($service->store($modelMock));
        $this->assertFalse($service->store($modelMock));
    }

    public function testHasChanges(): void
    {
        $data = ['fieldPaths' => ['field' => 'value'], 'hash' => '123'];

        $modelMock = $this->getMockBuilder(NormalizedModel::class)
            ->setConstructorArgs(
                [
                    'storage_key',
                    $data['fieldPaths'],
                    $data['hash'],
                ]
            )
            ->getMock();

        $this->adapter
            ->expects($this->exactly(2))
            ->method('loadHash')
            ->with($this->equalTo('storage_key'))
            ->willReturnOnConsecutiveCalls('123', 'changed');

        $service = new StorageService($this->factory, $this->adapter);
        $this->assertFalse($service->hasChanges($modelMock));
        $this->assertTrue($service->hasChanges($modelMock));
    }
}
