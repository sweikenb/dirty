<?php

namespace Sweikenb\Library\Dirty\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Api\ConfigInterface;
use Sweikenb\Library\Dirty\Factory;
use Sweikenb\Library\Dirty\Model\ConfigModel;
use Sweikenb\Library\Dirty\Model\DiffModel;
use Sweikenb\Library\Dirty\Model\DirtyCheckResultModel;
use Sweikenb\Library\Dirty\Model\NormalizedModel;

#[CoversClass(DirtyCheckResultModel::class)]
#[CoversClass(NormalizedModel::class)]
#[CoversClass(DiffModel::class)]
#[CoversClass(ConfigModel::class)]
#[CoversClass(Factory::class)]
class FactoryTest extends TestCase
{
    private ?Factory $factory = null;

    public function setUp(): void
    {
        $this->factory = new Factory();
    }

    public function testCreateResult(): void
    {
        $fields = ['foo' => new DiffModel('field', 'prev', 'curr')];

        $model = $this->factory->createResult($fields, null);

        $this->assertInstanceOf(DirtyCheckResultModel::class, $model);
        $this->assertTrue($model->isDirty);
        $this->assertSame($fields, $model->diffs);
    }

    public function testCreateNormalized(): void
    {
        $fields = ['foo' => 'bar'];

        $model = $this->factory->createNormalized('key', $fields, '123');

        $this->assertInstanceOf(NormalizedModel::class, $model);
        $this->assertSame($model->storageKey, 'key');
        $this->assertSame($model->fieldPaths, $fields);
        $this->assertSame($model->hash, '123');
    }

    public function testCreateConfig(): void
    {
        $check = ['foo' => 'bar'];
        $ignore = ['bar' => 'baz'];

        $model = $this->factory->createConfig($check, $ignore);

        $this->assertInstanceOf(ConfigInterface::class, $model);
        $this->assertInstanceOf(ConfigModel::class, $model);
        $this->assertSame($model->getFieldsToCheck(), $check);
        $this->assertSame($model->getFieldsToIgnore(), $ignore);
    }

    public function testCreateValueDiff(): void
    {
        $model = $this->factory->createDiff('foo', 'prev', 'curr');

        $this->assertInstanceOf(DiffModel::class, $model);
        $this->assertSame($model->fieldPath, 'foo');
        $this->assertSame($model->previously, 'prev');
        $this->assertSame($model->currently, 'curr');
    }
}
