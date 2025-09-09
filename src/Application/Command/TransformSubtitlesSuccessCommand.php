<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class TransformSubtitlesSuccessCommand implements CommandInterface
{
    public function __construct(
        public readonly string $subtitleAssFile,
        public readonly string $streamId,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
