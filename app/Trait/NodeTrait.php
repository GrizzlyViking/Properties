<?php

namespace App\Trait;

use App\Enums\Type;

/**
 * @property Type $type
 */
trait NodeTrait
{
    public function getHeight(): int
    {
        return $this->type->height();
    }

    public function getType(): string
    {
        return $this->type->value;
    }
}
