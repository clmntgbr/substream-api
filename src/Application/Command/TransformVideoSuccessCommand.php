<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class TransformVideoSuccessCommand implements CommandInterface
{
    public function __construct(
        public readonly string $fileNameTransformed,
        public readonly string $streamId,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
