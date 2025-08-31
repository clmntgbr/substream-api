<?php

namespace App\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class CreateStreamByUrlCommand
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly Uuid $userId,
        public readonly string $url,
    ) {
    }
}
