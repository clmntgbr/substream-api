<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
use App\Enum\JobStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorSuccessCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoProcessorSuccessCommand $command): void
    {
        $job = $this->jobRepository->findByJobId($command->jobId);
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $stream->setFileName($command->fileName);
            $stream->setOriginalFileName($command->originalFileName);
            $stream->setMimeType($command->mimeType);
            $stream->setSize($command->size);
            $stream->markAsUploaded();

            $this->commandBus->dispatch(new ExtractSoundCommand(
                streamId: $stream->getId(),
            ));
            $job->setStatus(JobStatusEnum::SUCCESS);
        } catch (\Throwable $exception) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $stream->markAsUploadFailed();
        } finally {
            $this->jobRepository->save($job, true);
            $this->streamRepository->save($stream, true);
        }
    }
}
