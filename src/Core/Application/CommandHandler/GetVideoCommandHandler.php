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
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private StreamRepository $streamRepository,
        private GetVideoProcessorInterface $processor,
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $job = $this->getCurrentJob();
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->processor)(new GetVideo($stream, $job));
        } catch (ProcessorException $exception) {
            $stream->markAsUploadFailed();
            $this->markJobAsFailure($exception->getMessage());
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
