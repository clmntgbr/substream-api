<?php

namespace App\Core\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class CreateUserModel extends AggregateRoot
{
    public function __construct(
        public Uuid $userId,
    ) {
    }

    public static function create(
        Uuid $userId,
    ): self {
        return new self(
            userId: $userId,
        );
    }
}
