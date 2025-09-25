<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\UploadFileName;
use App\Core\Domain\ValueObject\UploadOriginalFileName;
use App\Shared\Domain\Aggregate\AggregateRoot;

class UploadVideoModel extends AggregateRoot
{
    public function __construct(
        public ?UploadFileName $fileName,
        public ?UploadOriginalFileName $originalFileName,
        public ?StreamId $id,
    ) {
    }

    public static function create(
        ?UploadFileName $fileName,
        ?UploadOriginalFileName $originalFileName,
        ?StreamId $id,
    ): self {
        return new self(
            $fileName,
            $originalFileName,
            $id,
        );
    }
}
