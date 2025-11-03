<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Stream;
use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PublishService implements PublishServiceInterface
{
    public function __construct(
        private HubInterface $hub,
        private ElasticsearchRefreshService $elasticsearchRefreshService,
    ) {
    }

    public function refreshStream(Stream $stream, ?string $context = null): void
    {
        $this->elasticsearchRefreshService->refreshStreamIndex();

        $user = $stream->getUser();

        $data = json_encode([
            'type' => 'streams.refresh',
            'userId' => $user->getId(),
            'streamId' => $stream->getId(),
            'context' => $context,
        ]);

        if (false === $data) {
            return;
        }

        $streamUpdate = new Update(
            "/users/{$user->getId()}/streams/{$stream->getId()}",
            $data
        );

        $this->hub->publish($streamUpdate);
    }

    public function refreshSearchStreams(Stream $stream, ?string $context = null): void
    {
        $user = $stream->getUser();

        $data = json_encode([
            'type' => 'streams.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        if (false === $data) {
            return;
        }

        $update = new Update(
            "/users/{$user->getId()}/search/streams",
            $data
        );

        $this->hub->publish($update);
    }

    public function refreshSearchNotifications(User $user, ?string $context = null): void
    {
        $this->elasticsearchRefreshService->refreshNotificationIndex();

        $data = json_encode([
            'type' => 'notifications.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        if (false === $data) {
            return;
        }

        $update = new Update(
            "/users/{$user->getId()}/search/notifications",
            $data
        );

        $this->hub->publish($update);
    }
}
