<?php

namespace App\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class CreateStreamCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly Uuid $userId,
        public readonly UploadedFile $file,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return null;
    }
}
