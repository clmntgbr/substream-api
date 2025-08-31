<?php

namespace App\Application\Command;

use Symfony\Component\Uid\Uuid;

final class UploadVideoByUrlCommand
{
    public function __construct(
        public readonly string $url,
        public readonly Uuid $userId,
        public readonly Uuid $streamId,
    ) {
    }
}
