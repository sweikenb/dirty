<?php

use Sweikenb\Library\Dirty\Model\ConfigModel;
use Sweikenb\Library\Dirty\Service\DirtyCheckService;

require __DIR__.'/../vendor/autoload.php';
$rdata = require __DIR__.'/data/random_data.inc.php';

// config
$numItems = 1000;

// create items
$items = [];
for ($i = 0; $i < $numItems; ++$i) {
    $item = $rdata(mt_rand(5, 50));
    $items[] = $item;
    $items[] = $item;
}
$actualItems = count($items);
shuffle($items);

// get instance
$dirtyCheck = new DirtyCheckService();

// set exclude config
$exclude = new ConfigModel(ignoreFieldPath: [
    'owner.password',
    'owner.passwordSalt',
    'owner.passwordSalt',
    'owner.locationDetails.date',
    'owner.preferences.date',
    'reference.date',
    'tags.0.date',
    'tags.1.date',
    'categories.0.date',
    'categories.1.date',
    'createdAt',
    'updatedAt',
]);

// measure execution time
$start = microtime(true);
foreach ($items as $item) {
    $result = $dirtyCheck->execute($item['id'], $item, $exclude);
}
$finish = microtime(true);

// process results
$diff = $finish - $start;
fwrite(STDOUT, sprintf("\nItems processed: %s", $actualItems));
fwrite(STDOUT, sprintf("\nExecution Time: %s", $diff));
fwrite(STDOUT, sprintf("\nAvg. Time/Item: %s", $diff / $actualItems));
fwrite(STDOUT, "\n");
