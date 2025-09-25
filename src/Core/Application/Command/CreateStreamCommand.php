<?php

namespace App\Core\Application\Command;

use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Core\Domain\ValueObject\StreamUrl;
use App\Shared\Application\Command\AsyncCommandInterface;

class CreateStreamCommand implements AsyncCommandInterface
{
    public function __construct(
        public ?StreamId $id,
        public ?StreamFileName $fileName = null,
        public ?StreamOriginalFileName $originalFileName = null,
        public ?StreamUrl $url = null,
    ) {
    }
}
