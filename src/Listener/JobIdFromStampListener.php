<?php

declare(strict_types=1);

namespace App\Listener;

use App\Service\JobContextService;
use App\Shared\Infrastructure\Stamp\JobIdStamp;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

#[AsEventListener]
class JobIdFromStampListener
{
    public function __construct(
        private JobContextService $jobContextService,
    ) {
    }

    public function __invoke(WorkerMessageHandledEvent $event): void
    {
        $envelope = $event->getEnvelope();
        $jobIdStamp = $envelope->last(JobIdStamp::class);

        if ($jobIdStamp) {
            $this->jobContextService->setCurrentJobId($jobIdStamp->getJobId());
        }
    }
}
