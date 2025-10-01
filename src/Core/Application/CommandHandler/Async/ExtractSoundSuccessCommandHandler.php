<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\ExtractSoundSuccessCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class ExtractSoundSuccessCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ExtractSoundSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $stream->setAudioFiles($command->getAudioFiles());
        $stream->markAsExtractSoundCompleted();
        $this->streamRepository->save($stream);
    }
}
