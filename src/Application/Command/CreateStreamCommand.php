<?php

namespace App\Application\Command;

use Symfony\Component\Uid\Uuid;

final class CreateStreamCommand
{
    public function __construct(
        public readonly Uuid $uuid,
        public readonly string $fileName,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
    }
}
