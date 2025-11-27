<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Stringable;

abstract class ValueObject implements Stringable
{
    abstract public function __toString(): string;
}
