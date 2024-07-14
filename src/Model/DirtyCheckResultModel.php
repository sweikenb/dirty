<?php

namespace Sweikenb\Library\Dirty\Model;

class DirtyCheckResultModel
{
    /**
     * @param array<string, DiffModel> $diffs
     */
    public function __construct(
        public readonly bool $isDirty,
        public readonly array $diffs
    ) {
    }
}
