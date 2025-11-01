<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class UploadThumbnailCommand implements SyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private ?string $thumbnailUrl = null,
        private ?UploadedFile $thumbnail = null,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function getThumbnail(): ?UploadedFile
    {
        return $this->thumbnail;
    }
}
