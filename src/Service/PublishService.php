<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Stream;
use App\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PublishService implements PublishServiceInterface
{
    public function __construct(
        private HubInterface $hub,
        private CommandBusInterface $commandBus,
        private ElasticsearchRefreshService $elasticsearchRefreshService,
    ) {
    }

    public function refreshStream(Stream $stream, ?string $context = null): void
    {
        $this->elasticsearchRefreshService->refreshStreamIndex();

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
        $this->elasticsearchRefreshService->refreshNotificationIndex();

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
