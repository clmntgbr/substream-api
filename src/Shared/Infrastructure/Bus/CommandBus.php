<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Command\AsynchronousInterface;
use App\Shared\Application\Command\AsynchronousPriorityInterface;
use App\Shared\Application\Command\SynchronousInterface;
use Exception;
use RuntimeException;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

use function sprintf;

class CommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private MessageBusInterface $asyncCommandBus,
        private MessageBusInterface $asyncPriorityCommandBus,
    ) {
    }

    public function dispatch(object $command): mixed
    {
        if ($command instanceof SynchronousInterface) {
            return $this->dispatchSynchronous($command);
        }

        if ($command instanceof AsynchronousPriorityInterface) {
            return $this->dispatchAsynchronous($command);
        }

        if ($command instanceof AsynchronousInterface) {
            return $this->dispatchAsynchronous($command);
        }

        throw new RuntimeException('The message must implement SynchronousInterface or AsynchronousInterface or AsynchronousPriorityInterface.');
    }

    private function dispatchSynchronous(object $command): mixed
    {
        try {
            $envelope = $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $previousException = $exception->getPrevious();

            while (null !== $previousException) {
                if ($previousException instanceof Exception) {
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

        if (! $handledStamp) {
            throw new RuntimeException(sprintf('No handler found for command of type "%s".', $command::class));
        }

        return $handledStamp->getResult();
    }

    /**
     * @param AsynchronousInterface $command
     */
    private function dispatchAsynchronous(object $command): mixed
    {
        $this->asyncCommandBus->dispatch($command, $command->getStamps());

        return [
            'status' => 'queued',
            'command' => $command::class,
            'timestamp' => new DateTimeImmutable(),
        ];
    }
}
