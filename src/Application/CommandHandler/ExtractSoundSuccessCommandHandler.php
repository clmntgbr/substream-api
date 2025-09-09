<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ExtractSoundSuccessCommand;
use App\Application\Command\GenerateSubtitlesCommand;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ExtractSoundSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(ExtractSoundSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            return;
        }

        $stream->markAsExtractedSound($command->audioFiles);
        $this->streamRepository->save($stream);

        $this->messageBus->dispatch(new GenerateSubtitlesCommand(
            streamId: $stream->getId(),
        ));
    }
}
