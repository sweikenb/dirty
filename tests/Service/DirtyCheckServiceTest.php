<?php

namespace Sweikenb\Library\Dirty\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\DirtyCheckResultModel;
use Sweikenb\Library\Dirty\Model\NormalizedModel;
use Sweikenb\Library\Dirty\Service\DirtyCheckService;
use Sweikenb\Library\Dirty\Service\ModelDiffService;
use Sweikenb\Library\Dirty\Service\NormalizerService;
use Sweikenb\Library\Dirty\Service\StorageService;

#[CoversClass(DirtyCheckService::class)]
class DirtyCheckServiceTest extends TestCase
{
    private Factory|MockObject|null $factory = null;
    private NormalizerService|MockObject|null $normalizer = null;
    private StorageService|MockObject|null $storage = null;
    private ModelDiffService|MockObject|null $modelDiff = null;

    public function setUp(): void
    {
        $this->factory = $this->createMock(Factory::class);
        $this->normalizer = $this->createMock(NormalizerService::class);
        $this->storage = $this->createMock(StorageService::class);
        $this->modelDiff = $this->createMock(ModelDiffService::class);
    }

    public function testExecute(): void
    {
        $id = 'test:id';
        $data = ['test' => 'data'];
        $diffArray = ['test' => 'data'];

        $normModel = $this->createMock(NormalizedModel::class);
        $resultModel = $this->createMock(DirtyCheckResultModel::class);

        $this->normalizer
            ->expects($this->exactly(2))
            ->method('execute')
            ->with(
                $this->equalTo($id),
                $this->equalTo($data),
                $this->equalTo(null),
            )
            ->willReturn($normModel);

        $this->storage
            ->expects($this->exactly(1))
            ->method('hasChanges')
            ->with($this->equalTo($normModel))
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );

        $this->storage
            ->expects($this->exactly(2))
            ->method('getPreviousNormalization')
            ->with($this->equalTo($normModel))
            ->willReturn($normModel);

        $this->storage
            ->expects($this->exactly(2))
            ->method('store')
            ->with($this->equalTo($normModel));

        $this->modelDiff
            ->expects($this->exactly(2))
            ->method('execute')
            ->with(
                $this->equalTo($normModel),
                $this->equalTo($normModel)
            )
            ->willReturn($diffArray);

        $this->factory
            ->expects($this->exactly(2))
            ->method('createResult')
            ->withAnyParameters()
            ->willReturn($resultModel);

        $service = new DirtyCheckService(true, $this->factory, $this->normalizer, $this->storage, $this->modelDiff);
        $this->assertSame($resultModel, $service->execute($id, $data));

        $service = new DirtyCheckService(false, $this->factory, $this->normalizer, $this->storage, $this->modelDiff);
        $this->assertSame($resultModel, $service->execute($id, $data));
    }
}
