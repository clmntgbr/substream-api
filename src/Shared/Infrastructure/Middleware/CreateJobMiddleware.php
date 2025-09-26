<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Entity\Job;
use App\Service\JobContextService;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CreateJobMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobContextService $jobContextService,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof TrackableCommandInterface) {
            $job = new Job();
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        return $envelope;
    }
}
