<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
use App\Exception\JobNotFoundException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorSuccessCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private StreamRepository $streamRepository,
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
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
        } catch (\Throwable $exception) {
            $stream->markAsUploadFailed();
            $this->markJobAsFailure($exception->getMessage());
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
