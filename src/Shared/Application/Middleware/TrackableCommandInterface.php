<?php

declare(strict_types=1);

namespace App\Shared\Application\Middleware;

use Symfony\Component\Uid\Uuid;

interface TrackableCommandInterface
{
    public function getJobId(): Uuid;

    public function setJobId(Uuid $jobId): self;

    public function supports(): bool;
}
