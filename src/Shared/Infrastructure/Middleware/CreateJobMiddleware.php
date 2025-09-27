<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Service\JobContextService;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use App\Shared\Infrastructure\Stamp\JobIdStamp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CreateJobMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (!$message instanceof TrackableCommandInterface) {
            throw new \RuntimeException('The message must implement TrackableCommandInterface.');
        }

        if (true === $message->supports()) {
            $job = $this->createJob($message);
            $this->jobRepository->save($job, true);

            $envelope = $envelope->with(new JobIdStamp($job->getId()));
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        return $envelope;
    }

    private function createJob(object $message): Job
    {
        return Job::create($message);
    }
}
