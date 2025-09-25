<?php

namespace App\Core\Application\Query;

class FindByIdStreamQuery
{
    public function __construct(
        public ?\App\Core\Domain\ValueObject\StreamId $id,
    ) {
    }
}
