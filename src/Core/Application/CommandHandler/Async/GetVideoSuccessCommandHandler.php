<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Command\Async\GetVideoSuccessCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class GetVideoSuccessCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetVideoSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $stream->setFileName($command->getFileName());
        $stream->setOriginalFileName($command->getOriginalFileName());
        $stream->setMimeType($command->getMimeType());
        $stream->setSize($command->getSize());
        $stream->markAsUploaded();
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new ExtractSoundCommand(
            streamId: $stream->getId(),
            fileName: $command->getFileName(),
        ));
        
    }
}
