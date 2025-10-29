<?php

declare(strict_types=1);

namespace App\Core\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use Symfony\Component\Uid\Uuid;

class UploadFileModel extends AggregateRoot
{
    public function __construct(
        private string $fileName,
        private string $originalFileName,
        private Uuid $id,
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

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
