<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateNotificationCommand;
use App\Core\Application\Command\CreateStreamSuccessNotificationCommand;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamSuccessNotificationCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateStreamSuccessNotificationCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->commandBus->dispatch(new CreateNotificationCommand(
            title: 'stream_success',
            message: 'stream_success_message',
            context: 'stream',
            contextId: $stream->getId(),
        ));
    }
}
