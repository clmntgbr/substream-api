<?php

namespace App\Core\Application\Command;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadVideoCommand
{
    public function __construct(
        public UploadedFile $file,
    ) {
    }
}
