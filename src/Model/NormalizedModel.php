<?php

namespace Sweikenb\Library\Dirty\Model;

class NormalizedModel
{
    public function __construct(
        public readonly string $storageKey,
        public readonly array $fieldPaths,
        public readonly string $hash
    ) {
    }
}
