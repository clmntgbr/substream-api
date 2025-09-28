<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
use App\Core\Application\Trait\JobTrait;
use App\Exception\JobNotFoundException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorSuccessCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoProcessorSuccessCommand $command): void
    {
        $job = $this->getJob($command->jobId);

        if (null === $job) {
            throw new JobNotFoundException();
        }

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

            $this->markJobAsSuccess();

            $this->commandBus->dispatch(new ExtractSoundCommand(
                streamId: $stream->getId(),
            ));
        } catch (\Throwable $exception) {
            $stream->markAsUploadFailed();
            $this->markJobAsFailure($exception->getMessage());
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
