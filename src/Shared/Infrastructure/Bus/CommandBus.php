<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Exception\BusinessException;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $asyncCommandBus,
    ) {
    }

    public function dispatch(object $command): mixed
    {
        if (!$command instanceof SyncCommandInterface && !$command instanceof AsyncCommandInterface) {
            throw new \RuntimeException('The message must implement SyncCommandInterface or  AsyncCommandInterface.');
        }

        if ($command instanceof SyncCommandInterface) {
            return $this->dispatchSync($command);
        }

        return $this->dispatchAsync($command);
    }

    private function dispatchSync(object $command): mixed
    {
        try {
            $envelope = $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $previousException = $exception->getPrevious();

            while (null !== $previousException) {
                if ($previousException instanceof BusinessException) {
                    throw $previousException;
                }

                $previousException = $previousException->getPrevious();
            }

            $innerException = $exception->getPrevious();

            if (null !== $innerException) {
                throw $innerException;
            }

            throw $exception;
        }

        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp) {
            throw new \RuntimeException(sprintf('No handler found for command of type "%s".', $command::class));
        }

        return $handledStamp->getResult();
    }

    /**
     * @param AsyncCommandInterface $command
     */
    private function dispatchAsync(object $command): mixed
    {
        $this->asyncCommandBus->dispatch($command, $command->getStamps());

        return [
            'status' => 'queued',
            'command' => $command::class,
            'timestamp' => new \DateTimeImmutable(),
        ];
    }
}
