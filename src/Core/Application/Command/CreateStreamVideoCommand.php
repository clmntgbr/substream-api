<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamVideoCommand implements SyncCommandInterface
{
    private Uuid $streamId;

    public function __construct(
        private UploadedFile $file,
        private Uuid $optionId,
        private User $user,
    ) {
        $this->streamId = Uuid::v4();
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
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
