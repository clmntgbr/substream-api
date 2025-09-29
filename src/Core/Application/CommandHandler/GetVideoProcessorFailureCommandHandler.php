<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoProcessorFailureCommand;
use App\Enum\JobStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorFailureCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoProcessorFailureCommand $command): void
    {
        $job = $this->jobRepository->findByJobId($command->jobId);
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $job->setStatus(JobStatusEnum::FAILURE);
        $stream->markAsUploadFailed();
        $this->jobRepository->save($job, true);
        $this->streamRepository->save($stream, true);
    }
}
