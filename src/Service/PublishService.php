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

    public function dispatchSearchStreams(User $user): void
    {
        $this->commandBus->dispatch(new UpdateSearchStreamsCommand(
            userId: $user->getId(),
        ));
    }

    public function dispatchSearchNotifications(User $user): void
    {
        $this->commandBus->dispatch(new UpdateSearchNotificationsCommand(
            userId: $user->getId(),
        ));
    }

    public function refreshSearchStreams(User $user): void
    {
        $update = new Update(
            "/users/{$user->getId()}/search/streams",
            json_encode([
                'type' => 'streams.refresh',
                'userId' => $user->getId(),
            ])
        );

        $this->hub->publish($update);
    }

    public function refreshSearchNotifications(User $user): void
    {
        $update = new Update(
            "/users/{$user->getId()}/search/notifications",
            json_encode([
                'type' => 'notifications.refresh',
                'userId' => $user->getId(),
            ])
        );

        $this->hub->publish($update);
    }
}