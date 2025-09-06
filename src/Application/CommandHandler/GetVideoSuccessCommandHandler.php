<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ExtractSoundCommand;
use App\Application\Command\GetVideoSuccessCommand;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetVideoSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(GetVideoSuccessCommand $command): void
    {
        $this->messageBus->dispatch(new ExtractSoundCommand(
            streamId: $command->streamId,
        ));
    }
}
