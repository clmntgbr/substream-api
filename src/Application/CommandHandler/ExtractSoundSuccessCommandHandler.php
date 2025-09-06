<?php

namespace App\Application\CommandHandler;

use App\Application\Command\GenerateSubtitlesCommand;
use App\Application\Command\ExtractSoundSuccessCommand;
use App\Application\Command\GetVideoSuccessCommand;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ExtractSoundSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(ExtractSoundSuccessCommand $command): void
    {
        $this->messageBus->dispatch(new GenerateSubtitlesCommand(
            streamId: $command->streamId,
        ));
    }
}
