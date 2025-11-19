<?php

declare(strict_types=1);

namespace App\Application\Upload\Dto;

use Symfony\Component\Uid\Uuid;

class UploadedFileDto
{
    public function __construct(
        public Uuid $id,
        public string $fileName,
        public string $originalFileName,
    ) {
    }
}
