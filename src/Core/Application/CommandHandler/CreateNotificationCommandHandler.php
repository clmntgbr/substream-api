<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateNotificationCommand;
use App\Entity\Notification;
use App\Exception\StreamNotFoundException;
use App\Repository\NotificationRepository;
use App\Repository\StreamRepository;
use App\Service\PublishServiceInterface;
use App\Util\Slugify;
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
            throw new StreamNotFoundException($command->getContextId()->toRfc4122());
        }

        $originalFileName = $stream->getOriginalFileName();

        if (null === $originalFileName) {
            throw new \RuntimeException('file name is required');
        }

        $contextMessage = Slugify::slug($originalFileName);

        $notification = Notification::create(
            title: $command->getTitle(),
            message: $command->getMessage(),
            context: $command->getContext(),
            contextId: $command->getContextId(),
            user: $stream->getUser(),
            contextMessage: $contextMessage,
        );

        $this->notificationRepository->saveAndFlush($notification);
        $this->publishService->refreshSearchNotifications($stream->getUser(), CreateNotificationCommand::class);
    }
}
