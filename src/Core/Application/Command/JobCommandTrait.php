<?php

namespace App\Core\Application\Command;

use Symfony\Component\Uid\Uuid;

trait JobCommandTrait
{
    private ?Uuid $jobId = null;

    public function getJobId(): ?Uuid
    {
        return $this->jobId;
    }

    public function setJobId(Uuid $jobId): self
    {
        $this->jobId = $jobId;

        return $this;
    }
}
