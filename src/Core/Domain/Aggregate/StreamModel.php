<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Core\Domain\ValueObject\StreamId;
use App\Shared\Domain\Aggregate\AggregateRoot;

/**
 * Class Stream* Aggregate Root of the Stream context.
 */
class Stream extends AggregateRoot
{
    public function __construct(
        public ?StreamId $id,
    ) {
    }

    public static function create(
        ?StreamId $id,
    ): self {
        return new self(
            $id,
        );
    }

    public function update(
        ?StreamId $id,
    ): self {
        return new self(
            $id,
        );
    }
}
