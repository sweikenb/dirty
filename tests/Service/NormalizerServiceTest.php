<?php

namespace Sweikenb\Library\Dirty\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Sweikenb\Library\Dirty\Model\ConfigModel;
use Sweikenb\Library\Dirty\Service\NormalizerService;
use Sweikenb\Library\Dirty\Service\NormalizerService as NormalizerServiceAlias;
use Sweikenb\Library\Dirty\Tests\Testdata\Classes\Person;
use Sweikenb\Library\Dirty\Tests\Testdata\Classes\School;
use Sweikenb\Library\Dirty\Tests\Testdata\Classes\SchoolClass;

#[CoversClass(NormalizerServiceAlias::class)]
class NormalizerServiceTest extends TestCase
{
    public static function dataProvider(): array
    {
        $array = [
            'name' => 'ACME School',
            'classes' => [
                'A1' => [
                    'teacher' => ['name' => 'Mr. Mustermann', 'age' => 44],
                    'students' => [
                        ['name' => 'Peter', 'age' => 11],
                        ['name' => 'Anna', 'age' => 12],
                        ['name' => 'Ludger', 'age' => 12],
                    ],
                ],
            ],
        ];

        $object = new School(
            'ACME School',
            [
                'A1' => new SchoolClass(
                    new Person('Mr. Mustermann', 44),
                    [
                        new Person('Peter', 11),
                        new Person('Anna', 12),
                        new Person('Ludger', 12),
                    ]
                ),
            ]
        );

        return [
            [$array],
            [$object],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testNormalizeDefault(array|object $toCheck): void
    {
        $expected = [
            'classes.A1.students.0.age' => '11',
            'classes.A1.students.0.name' => 'Peter',
            'classes.A1.students.1.age' => '12',
            'classes.A1.students.1.name' => 'Anna',
            'classes.A1.students.2.age' => '12',
            'classes.A1.students.2.name' => 'Ludger',
            'classes.A1.teacher.age' => '44',
            'classes.A1.teacher.name' => 'Mr. Mustermann',
            'name' => 'ACME School',
        ];

        $result = (new NormalizerService())->execute('test', $toCheck);
        $this->assertSame($expected, $result->fieldPaths);
    }

    #[DataProvider('dataProvider')]
    public function testNormalizeExplicit(array|object $toCheck): void
    {
        $expected = [
            'classes.A1.teacher.age' => '44',
            'classes.A1.teacher.name' => 'Mr. Mustermann',
            'name' => 'ACME School',
        ];
        $result = (new NormalizerService())->execute(
            'test',
            $toCheck,
            new ConfigModel(checkFieldPath: ['name', 'classes.A1.teacher'])
        );
        $this->assertSame($expected, $result->fieldPaths);
    }

    #[DataProvider('dataProvider')]
    public function testNormalizeIgnore(array|object $toCheck): void
    {
        $expected = [
            'classes.A1.students.0.age' => '11',
            'classes.A1.students.0.name' => 'Peter',
            'classes.A1.students.1.age' => '12',
            'classes.A1.students.1.name' => 'Anna',
            'classes.A1.teacher.age' => '44',
            'classes.A1.teacher.name' => 'Mr. Mustermann',
        ];
        $result = (new NormalizerService())->execute(
            'test',
            $toCheck,
            new ConfigModel(ignoreFieldPath: ['name', 'classes.A1.students.2'])
        );
        $this->assertSame($expected, $result->fieldPaths);
    }

    #[DataProvider('dataProvider')]
    public function testNormalizeBoth(array|object $toCheck): void
    {
        $expected = [
            'classes.A1.students.0.age' => '11',
            'classes.A1.students.0.name' => 'Peter',
            'classes.A1.students.2.age' => '12',
            'classes.A1.students.2.name' => 'Ludger',
            'name' => 'ACME School',
        ];
        $result = (new NormalizerService())->execute(
            'test',
            $toCheck,
            new ConfigModel(
                checkFieldPath: ['name', 'classes.A1.students'],
                ignoreFieldPath: ['classes.A1.students.1']
            )
        );
        $this->assertSame($expected, $result->fieldPaths);
    }
}
