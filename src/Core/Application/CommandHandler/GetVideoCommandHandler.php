<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Client\Processor\GetVideoProcessorInterface;
use App\Core\Application\Command\GetVideoCommand;
use App\Dto\GetVideo;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Enum\JobStatusEnum;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private GetVideoProcessorInterface $processor,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $job = $this->jobRepository->findByJobId($command->getJobId());

        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->processor)(new GetVideo($stream, $command->getJobId()));
        } catch (ProcessorException $exception) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $stream->markAsUploadFailed();
        } finally {
            $this->jobRepository->save($job, true);
            $this->streamRepository->save($stream);
        }
    }
}
