<?php

namespace App\Service;

use App\Entity\Stream;
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

    public function refreshStream(Stream $stream, ?string $context = null): void
    {
        $user = $stream->getUser();

        $streamUpdate = new Update(
            "/users/{$user->getId()}/streams/{$stream->getId()}",
            json_encode([
                'type' => 'streams.refresh',
                'userId' => $user->getId(),
                'streamId' => $stream->getId(),
                'context' => $context,
            ])
        );

        $this->hub->publish($streamUpdate);
    }

    public function refreshSearchStreams(Stream $stream, ?string $context = null): void
    {
        $user = $stream->getUser();

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