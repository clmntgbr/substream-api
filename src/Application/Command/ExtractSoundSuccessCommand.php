<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

final class ExtractSoundSuccessCommand implements CommandInterface
{
    public function __construct(
        public readonly string $streamId,
        public readonly array $audioFiles,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
