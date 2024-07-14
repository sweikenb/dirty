<?php

use Sweikenb\Library\Dirty\Service\DirtyCheckService;

require __DIR__.'/../vendor/autoload.php';

$dirtyCheck = new DirtyCheckService();

/*
 * Execute this example multiple times to see the library in action
 */

$id = 'my_identifier';

$foo = [
    'some' => 'foo',
    'Bar' => 'baz',
    'foo' => microtime(true),
];

$result = $dirtyCheck->execute($id, $foo);
if ($result->isDirty) {
    foreach ($result->diffs as $fieldPath => $valueDiff) {
        echo sprintf("Field '%s' is dirty! '%s' -> '%s'\n", $fieldPath, $valueDiff->previously, $valueDiff->currently);
    }
}
