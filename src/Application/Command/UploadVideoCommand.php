<?php

namespace App\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class UploadVideoCommand
{
    public function __construct(
        public readonly UploadedFile $file,
    ) {
    }
}
