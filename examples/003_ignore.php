<?php

use Sweikenb\Library\Dirty\Model\ConfigModel;
use Sweikenb\Library\Dirty\Service\DirtyCheckService;

require __DIR__.'/../vendor/autoload.php';

$dirtyCheck = new DirtyCheckService();

$id = 'my_identifier';

$foo = [
    'some' => [
        'some' => 'foo',
        'Bar' => 'baz',
        'baz' => [
            'Baz' => 'foo',
        ],
    ],
    'Bar' => 'baz',
    'baz' => [
        'Baz' => [
            'some' => 'foo',
            'Bar' => [
                'some' => [
                    'some' => 'foo',
                    'Bar' => 'baz',
                    'baz' => [
                        'Baz' => [
                            'some' => 'foo',
                            'Bar' => 'baz',
                            'baz' => [
                                'Baz' => microtime(true), // dynamic value that would always result in a positive check
                            ],
                        ],
                    ],
                ],
                'Bar' => 'baz',
                'baz' => [
                    'Baz' => microtime(true), // dynamic value that would always result in a positive check
                ],
            ],
            'baz' => [
                'Baz' => 'foo',
            ],
        ],
    ],
];

// only compare values that match the following paths ("path must begin with"-wildcard)
$checkPaths = [
    'some',
    'baz.Baz.some',
    'baz.Baz.baz',
    'baz.Baz.Bar',
];

// ignore fields with dynamic contents such as timestamps ("path must begin with"-wildcard)
$ignorePaths = [
    'baz.Baz.Bar.baz.Baz',
    'baz.Baz.Bar.some.baz.Baz.baz',
];

$config = new ConfigModel($checkPaths, $ignorePaths);

$result = $dirtyCheck->execute($id, $foo, $config);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$result = $dirtyCheck->execute($id, $foo, $config);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";
