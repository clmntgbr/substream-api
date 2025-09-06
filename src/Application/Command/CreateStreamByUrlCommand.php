<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class CreateStreamByUrlCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly Uuid $userId,
        public readonly string $url,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return null;
    }
}
