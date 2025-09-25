<?php

namespace App\Core\Application\Command;

class CreateStream
{
    public function __construct(
        public string $fileName,
        public string $originalFileName,
        public string $url,
    ) {
    }
}