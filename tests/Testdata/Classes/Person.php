<?php

namespace Sweikenb\Library\Dirty\Tests\Testdata\Classes;

class Person
{
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {
    }
}
