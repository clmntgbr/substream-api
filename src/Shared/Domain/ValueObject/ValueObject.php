<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class ValueObject implements \Stringable
{
    abstract public function __toString(): string;
}
