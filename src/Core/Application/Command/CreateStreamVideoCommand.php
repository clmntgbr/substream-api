<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class CreateStreamVideoCommand implements SyncCommandInterface, TrackableCommandInterface
{
    private Uuid $streamId;
    private Uuid $jobId;

    public function getJobId(): Uuid
    {
        return $this->jobId;
    }

    public function setJobId(Uuid $jobId): self
    {
        $this->jobId = $jobId;

        return $this;
    }

    public function __construct(
        public UploadedFile $videoFile,
        public User $user,
    ) {
        $this->streamId = Uuid::v4();
        $this->jobId = Uuid::v4();
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

    public function supports(): bool
    {
        return true;
    }
}
