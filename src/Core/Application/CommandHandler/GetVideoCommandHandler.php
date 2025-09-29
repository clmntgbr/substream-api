<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Client\Processor\GetVideoProcessorInterface;
use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Trait\JobTrait;
use App\Dto\GetVideo;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private GetVideoProcessorInterface $processor,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $this->findByJobId($command->getJobId());

        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->processor)(new GetVideo($stream, $command->getJobId()));
        } catch (ProcessorException $exception) {
            $this->markJobAsFailure();
            $stream->markAsUploadFailed();
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
