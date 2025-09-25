<?php

declare(strict_types=1);

namespace App\CQRS\Query\Stream;

use App\CQRS\Query\QueryMessage;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetStreamQuery implements QueryMessage
{
    public function __construct(
        #[Assert\NotNull]
        public readonly Uuid $streamId
    ) {
    }
}
