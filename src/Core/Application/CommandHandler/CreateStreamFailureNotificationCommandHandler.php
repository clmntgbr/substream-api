<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateFailureStreamNotificationCommand;
use App\Core\Application\Command\CreateNotificationCommand;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamFailureNotificationCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateFailureStreamNotificationCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => CreateFailureStreamNotificationCommand::class,
            ]);

            return;
        }

        $this->commandBus->dispatch(new CreateNotificationCommand(
            title: 'stream_failure',
            message: 'stream_failure_message',
            context: 'stream',
            contextId: $stream->getId(),
        ));
    }
}
