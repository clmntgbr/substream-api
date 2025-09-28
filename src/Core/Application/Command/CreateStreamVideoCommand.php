<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class CreateStreamVideoCommand implements SyncCommandInterface, TrackableCommandInterface
{
    use JobCommandTrait;

    private Uuid $streamId;

    public function __construct(
        public UploadedFile $videoFile,
        public User $user,
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

    public function getData(): array
    {
        return [
            'streamId' => $this->getStreamId(),
        ];
    }

    public function supports(): bool
    {
        return true;
    }
}
