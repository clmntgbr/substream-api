<?php

declare(strict_types=1);

namespace App\CQRS\Command\Stream;

use App\CQRS\Stamp\JobIdStamp;
use App\Entity\Job;
use App\Enum\JobStatusEnum;
use App\Service\StreamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
class CreateStreamCommandHandlerV2 implements MessageHandlerInterface
{
    public function __construct(
        private StreamService $streamService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(CreateStreamCommand $command, Envelope $envelope): Uuid
    {
        // Récupérer le JobIdStamp depuis l'enveloppe
        $jobIdStamp = $envelope->last(JobIdStamp::class);
        $job = null;
        
        if ($jobIdStamp) {
            $job = $this->entityManager->find(Job::class, $jobIdStamp->getJobId());
            if ($job) {
                $job->setStatus(JobStatusEnum::RUNNING);
                $this->entityManager->flush();
            }
        }

        try {
            $stream = $this->streamService->createStream(
                $command->fileName,
                $command->originalFileName,
                $command->mimeType,
                $command->size,
                $command->url,
                $command->user,
                $command->options
            );

            if ($job) {
                $job->setStatus(JobStatusEnum::SUCCESS);
                $job->setMetadata(['streamId' => $stream->getId()->toRfc4122()]);
            }

            return $stream->getId();
        } catch (\Throwable $exception) {
            if ($job) {
                $job->setStatus(JobStatusEnum::FAILURE);
                $job->setErrorMessage($exception->getMessage());
                $job->setErrorTrace($exception->getTraceAsString());
            }
            throw $exception;
        } finally {
            if ($job) {
                $this->entityManager->flush();
            }
        }
    }
}
