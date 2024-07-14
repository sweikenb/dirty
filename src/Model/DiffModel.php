<?php

namespace Sweikenb\Library\Dirty\Model;

class DiffModel
{
    public function __construct(
        public readonly string $fieldPath,
        public readonly mixed $previously,
        public readonly mixed $currently
    ) {
    }
}
