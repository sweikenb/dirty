<?php

namespace Sweikenb\Library\Dirty\Tests\Testdata\Classes;

class School
{
    public function __construct(
        public readonly string $name,
        private array $classes,
    ) {
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }
}
