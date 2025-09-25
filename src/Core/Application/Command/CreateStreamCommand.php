<?php

namespace App\Core\Application\Command;

use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Core\Domain\ValueObject\StreamUrl;

class CreateStreamCommand
{
    public function __construct(
        public ?StreamFileName $fileName,
        public ?StreamOriginalFileName $originalFileName,
        public ?StreamUrl $url,
    ) {
    }
}
