<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Uid\Uuid;

class JobIdStamp implements StampInterface
{
    public function __construct(
        private Uuid $jobId,
    ) {
    }

    public function getJobId(): Uuid
    {
        return $this->jobId;
    }
}
