<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Shared\Application\Middleware\TrackableCommandInterface;
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
            $job = $this->getJob($message);
            $this->jobRepository->save($job, true);
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        return $envelope;
    }

    private function getJob(TrackableCommandInterface $message): Job
    {
        try {
            $job = $this->jobRepository->findOneBy(['commandId' => $message->getCommandId()]);
            if ($job instanceof Job) {
                return $job;
            }
        } catch (\Throwable $exception) {
            dd('error 1', $exception);
            throw $exception;
        }

        try {
            return Job::create($message);
        } catch (\Throwable $exception) {
            dd('error 2', $exception);
            throw $exception;
        }
    }
}
