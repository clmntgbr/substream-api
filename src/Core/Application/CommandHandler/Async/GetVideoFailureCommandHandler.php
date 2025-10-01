<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\GetVideoFailureCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class GetVideoFailureCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetVideoFailureCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $stream->markAsUploadFailed();
        $this->streamRepository->save($stream);
    }
}
