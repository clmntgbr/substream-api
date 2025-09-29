<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
use App\Core\Application\Trait\JobTrait;
use App\Enum\JobStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorSuccessCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private JobRepository $jobRepository,
    ) {
        $this->jobRepository = $jobRepository;
    }

    public function __invoke(GetVideoProcessorSuccessCommand $command): void
    {
        $this->findByJobId($command->jobId);
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
            $this->markJobAsSuccess();
        } catch (\Throwable $exception) {
            $this->markJobAsFailure();
            $stream->markAsUploadFailed();
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
