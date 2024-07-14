<?php

namespace Sweikenb\Library\Dirty\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Model\NormalizedModel;
use Sweikenb\Library\Dirty\Model\DiffModel;
use Sweikenb\Library\Dirty\Service\ModelDiffService;

#[CoversClass(ModelDiffService::class)]
#[CoversClass(DiffModel::class)]
class ModelDiffServiceTest extends TestCase
{
    private ?ModelDiffService $service = null;

    public function setUp(): void
    {
        $this->service = new ModelDiffService();
    }

    public function testExecuteNoPrevious(): void
    {
        $current = new NormalizedModel('skey', [
            'a' => 'b',
            'b.c.d' => 'e',
            'e.f' => 'g',
        ], 'hash');

        $expectedKeys = [
            'a',
            'b.c.d',
            'e.f',
        ];

        $diff = $this->service->execute(null, $current);

        // ensure we do not have more or less diffs than expected
        $this->assertCount(count($expectedKeys), $diff);

        // check diff
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $diff);
        }

        // sorted by array key
        $this->assertSame($expectedKeys, array_keys($diff));
    }

    public function testExecuteNoChange(): void
    {
        $previous = new NormalizedModel('skey', [
            'a' => 'b',
            'b.c.d' => 'e',
            'e.f' => 'g',
        ], 'hash');

        $current = new NormalizedModel('skey', [
            'a' => 'b',
            'b.c.d' => 'e',
            'e.f' => 'g',
        ], 'hash');

        $diff = $this->service->execute($previous, $current);
        $this->assertEmpty($diff);
    }

    public function testExecuteChange(): void
    {
        $previous = new NormalizedModel('skey', [
            'a' => 'd',
            'b.c.d' => 'e',
            'e.f' => 'g',
        ], 'hash');

        $current = new NormalizedModel('skey', [
            'a' => 'b',
            'e.f' => 'p',
            'z' => 'y',
        ], 'hash');

        $expectedKeys = [
            'a' => ['d', 'b'],
            'b.c.d' => ['e', null],
            'e.f' => ['g', 'p'],
            'z' => [null, 'y'],
        ];

        $diff = $this->service->execute($previous, $current);

        // ensure we do not have more or less diffs than expected
        $this->assertCount(count($expectedKeys), $diff);

        // check diff
        foreach ($expectedKeys as $fieldPath => $expectedDiff) {
            $this->assertArrayHasKey($fieldPath, $diff);
            $this->assertInstanceOf(DiffModel::class, $diff[$fieldPath]);

            $valueDiff = $diff[$fieldPath];
            $this->assertSame($fieldPath, $valueDiff->fieldPath);
            $this->assertSame($expectedDiff[0], $valueDiff->previously);
            $this->assertSame($expectedDiff[1], $valueDiff->currently);
        }

        // sorted by array key
        $this->assertSame(array_keys($expectedKeys), array_keys($diff));
    }
}
