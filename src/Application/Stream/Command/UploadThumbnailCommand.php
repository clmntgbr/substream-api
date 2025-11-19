<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Shared\Application\Command\SynchronousInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class UploadThumbnailCommand implements SynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
        private ?UploadedFile $thumbnail = null,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getThumbnail(): ?UploadedFile
    {
        return $this->thumbnail;
    }
}
