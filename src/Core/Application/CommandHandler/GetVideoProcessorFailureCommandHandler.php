<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoProcessorFailureCommand;
use App\Core\Application\Trait\JobTrait;
use App\Enum\JobStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoProcessorFailureCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private JobRepository $jobRepository,
    ) {
        $this->jobRepository = $jobRepository;
    }

    public function __invoke(GetVideoProcessorFailureCommand $command): void
    {
        $this->findByJobId($command->jobId);
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->markJobAsFailure();
        $stream->markAsUploadFailed();
        $this->streamRepository->save($stream, true);
    }
}
