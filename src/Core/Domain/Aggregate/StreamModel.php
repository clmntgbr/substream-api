<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Core\Domain\ValueObject\StreamUrl;
use App\Shared\Domain\Aggregate\AggregateRoot;

class StreamModel extends AggregateRoot
{
    public function __construct(
        public ?StreamFileName $fileName,
        public ?StreamOriginalFileName $originalFileName,
        public ?StreamUrl $url,
        public ?StreamId $id,
    ) {
    }

    public static function create(
        ?StreamFileName $fileName,
        ?StreamOriginalFileName $originalFileName,
        ?StreamUrl $url,
        ?StreamId $id,
    ): self {
        return new self(
            $fileName,
            $originalFileName,
            $url,
            $id,
        );
    }
}
