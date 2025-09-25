<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

/**
 * Class CreateStreamCommand Represents a command for creating a Stream.
 */
class CreateStreamCommand
{
    public function __construct(
        public ?\App\Core\Domain\ValueObject\StreamFileName $fileName,
        public ?\App\Core\Domain\ValueObject\StreamOriginalFileName $originalFileName,
        public ?\App\Core\Domain\ValueObject\StreamUrl $url,
    ) {
    }
}
