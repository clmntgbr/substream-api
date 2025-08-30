<?php

namespace App\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadVideoCommand
{
    public function __construct(
        public readonly UploadedFile $file,
    ) {
    }
}
