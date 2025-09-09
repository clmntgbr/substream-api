<?php

namespace App\Application\CommandHandler;

use App\Application\Command\GenerateSubtitlesSuccessCommand;
use App\Application\Command\TransformSubtitlesCommand;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateSubtitlesSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(GenerateSubtitlesSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            return;
        }

        $stream->markAsGeneratedSubtitles($command->subtitleSrtFile, $command->subtitleSrtFiles);
        $this->streamRepository->save($stream);

        $this->messageBus->dispatch(new TransformSubtitlesCommand(
            streamId: $stream->getId(),
        ));
    }
}
