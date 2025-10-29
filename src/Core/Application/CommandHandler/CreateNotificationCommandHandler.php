<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateNotificationCommand;
use App\Entity\Notification;
use App\Exception\StreamNotFoundException;
use App\Repository\NotificationRepository;
use App\Repository\StreamRepository;
use App\Service\PublishServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNotificationCommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private StreamRepository $streamRepository,
        private PublishServiceInterface $publishService,
    ) {
    }

    public function __invoke(CreateNotificationCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getContextId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $notification = Notification::create(
            title: $command->getTitle(),
            message: $command->getMessage(),
            context: $command->getContext(),
            contextId: $command->getContextId(),
            user: $stream->getUser(),
            contextMessage: $stream->getOriginalFileName(),
        );

        $this->notificationRepository->save($notification, true);
        $this->publishService->dispatchSearchNotifications($stream->getUser(), CreateNotificationCommand::class);
    }
}
