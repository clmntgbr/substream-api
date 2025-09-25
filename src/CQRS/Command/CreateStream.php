<?php

namespace App\CQRS\Command;

class CreateStream
{
    public function __construct(
        public string $fileName,
        public string $originalFileName,
        public string $url,
    ) {
    }
}