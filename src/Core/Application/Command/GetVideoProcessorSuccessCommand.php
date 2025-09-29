<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorSuccessCommand implements AsyncCommandInterface, TrackableCommandInterface
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
        public readonly string $fileName,
        public readonly string $originalFileName,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
        $this->jobId = Uuid::v4();
    }

    public function supports(): bool
    {
        return false;
    }
}
