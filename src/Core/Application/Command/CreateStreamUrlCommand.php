<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class CreateStreamUrlCommand implements SyncCommandInterface, TrackableCommandInterface
{
    private Uuid $streamId;
    private Uuid $jobId;

    public function __construct(
        public string $url,
        public User $user,
    ) {
        $this->streamId = Uuid::v4();
        $this->jobId = Uuid::v4();
    }

    public function getJobId(): Uuid
    {
        return $this->jobId;
    }

    public function setJobId(Uuid $jobId): self
    {
        $this->jobId = $jobId;

        return $this;
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function supports(): bool
    {
        return true;
    }
}
