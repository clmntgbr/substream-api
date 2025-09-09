<?php

namespace App\Application\Command;

use App\Dto\UploadVideoOptions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class CreateStreamCommand implements CommandInterface
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly Uuid $userId,
        public readonly UploadedFile $file,
        public readonly UploadVideoOptions $options,
    ) {
    }

    public function getAmqpStamp(): ?AmqpStamp
    {
        return null;
    }
}
