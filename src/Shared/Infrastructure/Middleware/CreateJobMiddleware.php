<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use App\Entity\Job;
use App\Enum\JobStatusEnum;
use App\Service\JobContextService;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use App\Shared\Infrastructure\Stamp\JobIdStamp;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

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
            $job->setStatus(JobStatusEnum::PENDING);
            $job->setCommandClass(get_class($message));
            $job->setCommandData($this->serializeMessage($message));

            $this->entityManager->persist($job);
            $this->entityManager->flush();

            $this->jobContextService->setCurrentJobId($job->getId());
            $envelope = $envelope->with(new JobIdStamp($job->getId()));
        }

        $envelope = $stack->next()->handle($envelope, $stack);

        if ($message instanceof TrackableCommandInterface) {
            $handledStamp = $envelope->last(HandledStamp::class);
            if ($handledStamp) {
                $jobId = $envelope->last(JobIdStamp::class)?->getJobId();
                $envelope = $envelope->with(new HandledStamp($jobId, $handledStamp->getHandlerName()));
            }

            $this->jobContextService->clearCurrentJobId();
        }

        return $envelope;
    }

    private function serializeMessage(object $message): array
    {
        $reflection = new \ReflectionClass($message);
        $data = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $data[$property->getName()] = (string) $property->getValue($message);
        }

        return $data;
    }
}
