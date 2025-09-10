<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ExtractSoundCommand;
use App\Application\Command\GetVideoSuccessCommand;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetVideoSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(GetVideoSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            return;
        }

        $stream->updateStream($command->fileName, $command->originalFileName, $command->mimeType, $command->size);
        $stream->markAsUploaded();

        $this->streamRepository->save($stream);

        $this->messageBus->dispatch(new ExtractSoundCommand(
            streamId: $stream->getId(),
        ));
    }
}
