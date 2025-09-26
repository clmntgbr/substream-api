<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Uid\Uuid;

class JobContextService
{
    private ?Uuid $currentJobId = null;

    public function setCurrentJobId(Uuid $jobId): void
    {
        $this->currentJobId = $jobId;
    }

    public function getCurrentJobId(): ?Uuid
    {
        return $this->currentJobId;
    }

    public function clearCurrentJobId(): void
    {
        $this->currentJobId = null;
    }
}
