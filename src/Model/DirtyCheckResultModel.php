<?php

namespace Sweikenb\Library\Dirty\Model;

class DirtyCheckResultModel
{
    private mixed $updateStore;

    /**
     * @param array<string, DiffModel> $diffs
     */
    public function __construct(
        public readonly bool $isDirty,
        public readonly array $diffs,
        ?callable $updateStore
    ) {
        $this->updateStore = $updateStore;
    }

    public function updateStore(): void
    {
        if (is_callable($this->updateStore)) {
            call_user_func($this->updateStore);
            $this->updateStore = null;
        }
    }
}
