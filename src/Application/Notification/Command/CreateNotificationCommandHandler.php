<?php

declare(strict_types=1);

namespace App\Application\Notification\Command;

use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Domain\Stream\Repository\StreamRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Utils\Slugify;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNotificationCommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private StreamRepository $streamRepository,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(CreateNotificationCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getContextId());

        if (null === $stream) {
            throw new \Exception($command->getContextId()->toRfc4122());
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
        $this->mercurePublisher->refreshNotifications($stream->getUser(), CreateNotificationCommand::class);
    }
}
