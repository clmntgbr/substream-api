<?php

namespace App\Core\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class CreateStreamModel extends AggregateRoot
{
    public function __construct(
        public Uuid $streamId,
    ) {
    }

    public static function create(
        Uuid $streamId,
    ): self {
        return new self(
            streamId: $streamId,
        );
    }
}
