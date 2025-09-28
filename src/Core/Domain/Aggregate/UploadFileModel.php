<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class UploadFileModel extends AggregateRoot
{
    public function __construct(
        public string $fileName,
        public string $originalFileName,
        public Uuid $id,
    ) {
    }

    public static function create(
        string $fileName,
        string $originalFileName,
        Uuid $id,
    ): self {
        return new self(
            $fileName,
            $originalFileName,
            $id,
        );
    }
}
