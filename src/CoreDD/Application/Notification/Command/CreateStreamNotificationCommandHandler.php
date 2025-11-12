<?php

declare(strict_types=1);

namespace App\CoreDD\Application\CommandHandler;

use App\CoreDD\Application\Command\CreateStreamNotificationCommand;
use App\CoreDD\Application\Notification\Command\CreateNotificationCommand;
use App\CoreDD\Domain\Stream\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamNotificationCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateStreamNotificationCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => CreateStreamNotificationCommand::class,
            ]);

            return;
        }

        $this->commandBus->dispatch(new CreateNotificationCommand(
            title: match ($command->getStatus()) {
                'success' => 'stream_success',
                'failure' => 'stream_failure',
                default => throw new \InvalidArgumentException('Invalid status'),
            },
            message: match ($command->getStatus()) {
                'success' => 'stream_success_message',
                'failure' => 'stream_failure_message',
                default => throw new \InvalidArgumentException('Invalid status'),
            },
            context: 'stream',
            contextId: $stream->getId(),
        ));
    }
}
