<?php

namespace App\Service;

use App\Core\Application\Command\UpdateSearchNotificationsCommand;
use App\Core\Application\Command\UpdateSearchStreamsCommand;
use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Shared\Application\Bus\CommandBusInterface;

class PublishService implements PublishServiceInterface
{
    public function __construct(
        private HubInterface $hub,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function dispatchSearchStreams(User $user, ?string $context = null): void
    {
        $this->commandBus->dispatch(new UpdateSearchStreamsCommand(
            userId: $user->getId(),
            context: $context,
        ));
    }

    public function dispatchSearchNotifications(User $user, ?string $context = null): void
    {
        $this->commandBus->dispatch(new UpdateSearchNotificationsCommand(
            userId: $user->getId(),
            context: $context,
        ));
    }

    public function refreshSearchStreams(User $user, ?string $context = null): void
    {
        $update = new Update(
            "/users/{$user->getId()}/search/streams",
            json_encode([
                'type' => 'streams.refresh',
                'userId' => $user->getId(),
                'context' => $context,
            ])
        );

        $this->hub->publish($update);
    }

    public function refreshSearchNotifications(User $user, ?string $context = null): void
    {
        $update = new Update(
            "/users/{$user->getId()}/search/notifications",
            json_encode([
                'type' => 'notifications.refresh',
                'userId' => $user->getId(),
                'context' => $context,
            ])
        );

        $this->hub->publish($update);
    }
}