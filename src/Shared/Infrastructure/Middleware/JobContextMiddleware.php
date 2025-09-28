<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Service\JobContextService;
use App\Shared\Infrastructure\Stamp\JobIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class JobContextMiddleware implements MiddlewareInterface
{
    public function __construct(
        private JobContextService $jobContextService,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $jobIdStamp = $envelope->last(JobIdStamp::class);
        
        if ($jobIdStamp) {
            $this->jobContextService->setCurrentJobId($jobIdStamp->getJobId());
        }

        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } finally {
            // Nettoyer le contexte après traitement
            $this->jobContextService->clearCurrentJobId();
        }

        return $envelope;
    }
}
