<?php

namespace App\Models;

interface NodeInterface
{
    public function getHeight(): int;

    public function getType(): string;
}
