<?php

use Sweikenb\Library\Dirty\Service\DirtyCheckService;

require __DIR__.'/../vendor/autoload.php';

$dirtyCheck = new DirtyCheckService();

// NOTE: time only added, so you can re-run this example over and over again, in reality you would use an entity ID
// or other unique identifier for the provided array or object.
$id = 'my_identifier_'.time();

$foo = [
    'some' => 'foo',
    'Bar' => 'baz',
];

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$foo = [
    'some' => 'foo',
    'Bar' => 'Baz',
];

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$foo = [
    'some' => 'foo',
    'Bar' => 'Baz',
    'New' => 'field',
];

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$foo = [
    'some' => 'foo',
];

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";

$result = $dirtyCheck->execute($id, $foo);
echo $result->isDirty
    ? 'DIRTY fields: ['.implode(', ', $result->diffs)."]\n"
    : "NOT dirty\n";
