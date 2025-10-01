<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\ExtractSoundFailureCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class ExtractSoundFailureCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExtractSoundFailureCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $stream->markAsExtractSoundFailed();
        $this->streamRepository->save($stream);
    }
}
