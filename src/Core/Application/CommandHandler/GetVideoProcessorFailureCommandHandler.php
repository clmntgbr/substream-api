<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoProcessorFailureCommand;
use App\Core\Application\Trait\JobTrait;
use App\Exception\JobNotFoundException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorFailureCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoProcessorFailureCommand $command): void
    {
        $job = $this->getJob($command->jobId);

        if (null === $job) {
            throw new JobNotFoundException();
        }

        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->markJobAsFailure($command->getErrorMessage());
        $stream->markAsUploadFailed();
        $this->streamRepository->save($stream, true);
    }
}
