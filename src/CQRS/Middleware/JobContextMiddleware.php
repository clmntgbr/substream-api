<?php

declare(strict_types=1);

namespace App\CQRS\Middleware;

use App\CQRS\Service\JobContextService;
use App\CQRS\Stamp\JobIdStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class JobContextMiddleware implements MiddlewareInterface
{
    public function __construct(
        private JobContextService $jobContextService
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Récupérer le JobIdStamp s'il existe
        $jobIdStamp = $envelope->last(JobIdStamp::class);
        
        if ($jobIdStamp) {
            $this->jobContextService->setCurrentJobId($jobIdStamp->getJobId());
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        // Nettoyer le contexte après traitement
        $this->jobContextService->clearCurrentJobId();

        return $envelope;
    }
}
