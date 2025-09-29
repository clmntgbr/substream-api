<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorFailureCommand implements AsyncCommandInterface, TrackableCommandInterface
{
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
        public Uuid $jobId,
        public readonly ?string $errorMessage = null,
    ) {
    }

    public function supports(): bool
    {
        return false;
    }
}
