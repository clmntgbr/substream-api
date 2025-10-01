<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Client\Processor\GetVideoProcessorInterface;
use App\Core\Application\Command\Async\GetVideoCommand;
use App\Dto\GetVideo;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class GetVideoCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        // try {
        //     ($this->processor)(new GetVideo($stream));
        // } catch (ProcessorException $exception) {
        //     $stream->markAsUploadFailed();
        // } finally {
        //     $this->streamRepository->save($stream);
        // }
    }
}
