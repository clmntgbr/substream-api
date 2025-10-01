<?php

namespace App\Core\Application\Command\Sync;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamVideoCommand implements SyncCommandInterface
{
    private Uuid $streamId;

    public function __construct(
        private UploadedFile $videoFile,
        private User $user,
    ) {
        $this->streamId = Uuid::v4();
    }

    public function getVideoFile(): UploadedFile
    {
        return $this->videoFile;
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
