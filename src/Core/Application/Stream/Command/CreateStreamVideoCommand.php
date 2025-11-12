<?php

declare(strict_types=1);

namespace App\Core\Application\Stream\Command;

use App\Core\Domain\User\Entity\User;
use App\Shared\Application\Command\SynchronousInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamVideoCommand implements SynchronousInterface
{
    private Uuid $streamId;

    public function __construct(
        private UploadedFile $file,
        private UploadedFile $thumbnail,
        private string $duration,
        private Uuid $optionId,
        private User $user,
    ) {
        $this->streamId = Uuid::v7();
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function getThumbnail(): UploadedFile
    {
        return $this->thumbnail;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }
}
