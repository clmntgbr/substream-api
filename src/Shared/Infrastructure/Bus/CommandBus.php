<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $asyncCommandBus,
    ) {
    }

    public function dispatch(object $command, array $stamps = []): mixed
    {
        if (!$command instanceof SyncCommandInterface && !$command instanceof AsyncCommandInterface) {
            throw new \RuntimeException('The message must implement SyncCommandInterface or  AsyncCommandInterface.');
        }

        if ($command instanceof SyncCommandInterface) {
            return $this->dispatchSync($command, $stamps);
        }

        if ($command instanceof AsyncCommandInterface) {
            return $this->dispatchAsync($command);
        }

        return $this->dispatchSync($command);
    }

    private function dispatchSync(object $command): mixed
    {
        $envelope = $this->commandBus->dispatch($command);
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp) {
            throw new \RuntimeException(sprintf('No handler found for command of type "%s".', $command::class));
        }

        return $handledStamp->getResult();
    }

    private function dispatchAsync(object $command, array $stamps = []): mixed
    {
        $this->asyncCommandBus->dispatch($command, $stamps);

        return [
            'status' => 'queued',
            'command' => $command::class,
            'timestamp' => new \DateTimeImmutable(),
        ];
    }
}
