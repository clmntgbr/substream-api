<?php

namespace App\Service;

use App\Application\Command\CommandInterface;
use App\Client\Processor\ExtractSoundProcessor;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\MessageBusInterface as SymfonyMessageBusInterface;

class MessageBus implements MessageBusInterface
{
    public function __construct(
        private ExtractSoundProcessor $extractSoundProcessor,
        private StreamRepository $streamRepository,
        private SymfonyMessageBusInterface $messageBus,
    ) {
    }

    public function dispatch(CommandInterface $command): void
    {
        $this->messageBus->dispatch($command, $command->getAmqpStamp() ? [$command->getAmqpStamp()] : []);
    }

    /**
     * @param CommandInterface[] $commands
     */
    public function dispatchs(array $commands): void
    {
        array_map(fn (CommandInterface $command) => $this->messageBus->dispatch($command, $command->getAmqpStamp() ? [$command->getAmqpStamp()] : []), $commands);
    }
}
