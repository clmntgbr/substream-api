<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Core\Domain\ValueObject\StreamUrl;
use App\Core\Domain\ValueObject\UploadVideoFileName;
use App\Shared\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class UploadVideoModel extends AggregateRoot
{
    public function __construct(
        public ?UploadVideoFileName $fileName,
        public ?Uuid $id,
    ) {
    }

    public static function create(
        ?UploadVideoFileName $fileName,
        ?Uuid $id,
    ): self {
        return new self(
            $fileName,
            $id,
        );
    }
}
