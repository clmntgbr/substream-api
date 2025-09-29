<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Entity\Job;
use App\Repository\JobRepository;
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
        private JobRepository $jobRepository,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof TrackableCommandInterface && $message->supports()) {
            $this->createJob($message);
        }

        $envelope = $stack->next()->handle($envelope, $stack);
        return $envelope;
    }

    private function createJob(TrackableCommandInterface $message): Job
    {
        $job = $this->jobRepository->findByJobId($message->getJobId());
        if ($job instanceof Job) {
            return $job;
        }

        $job = Job::create($message);
        $this->jobRepository->save($job, true);

        return $job;
    }
}
