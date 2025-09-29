<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Entity\User;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoCommand implements AsyncCommandInterface, TrackableCommandInterface
{
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
        public Uuid $streamId,
        public User $user,
        public string $url,
    ) {
        $this->jobId = Uuid::v4();
    }

    public function supports(): bool
    {
        return true;
    }
}
