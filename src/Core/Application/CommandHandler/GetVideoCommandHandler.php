<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Client\Processor\GetVideoProcessorInterface;
use App\Core\Application\Command\GetVideoCommand;
use App\Dto\GetVideo;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoCommandHandler
{

    public function __construct(
        private StreamRepository $streamRepository,
        private GetVideoProcessorInterface $processor,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->processor)(new GetVideo($stream));
        } catch (ProcessorException $exception) {
            $stream->markAsUploadFailed();
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
