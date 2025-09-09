<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class GenerateSubtitlesSuccessCommand implements CommandInterface
{
    public function __construct(
        public readonly array $subtitleSrtFiles,
        public readonly string $subtitleSrtFile,
        public readonly string $streamId,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
