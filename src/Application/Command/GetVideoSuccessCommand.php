<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class GetVideoSuccessCommand implements CommandInterface
{
    public function __construct(
        public readonly string $streamId,
        public readonly string $videoFileName,
        public readonly string $originalName,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
