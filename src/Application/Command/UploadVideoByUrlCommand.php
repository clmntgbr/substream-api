<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class UploadVideoByUrlCommand implements CommandInterface
{
    public function __construct(
        public readonly string $url,
        public readonly Uuid $userId,
        public readonly Uuid $streamId,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return new AmqpStamp('async-high');
    }
}
