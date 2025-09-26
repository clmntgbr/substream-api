<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Core\Domain\ValueObject\FileName;
use App\Core\Domain\ValueObject\OriginalFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\Url;
use App\Shared\Domain\Aggregate\AggregateRoot;

class StreamModel extends AggregateRoot
{
    public function __construct(
        public ?FileName $fileName,
        public ?OriginalFileName $originalFileName,
        public ?Url $url,
        public ?StreamId $id,
    ) {
    }

    public static function create(
        ?FileName $fileName,
        ?OriginalFileName $originalFileName,
        ?Url $url,
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
