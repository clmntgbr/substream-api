<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamCommand implements SyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private Uuid $optionId,
        private User $user,
        private ?string $fileName = null,
        private ?string $originalFileName = null,
        private ?int $duration = null,
        private ?string $url = null,
        private ?string $mimeType = null,
        private ?int $size = null,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }
}
