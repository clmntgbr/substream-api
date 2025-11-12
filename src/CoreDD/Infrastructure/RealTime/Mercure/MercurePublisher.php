<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\RealTime\Mercure;

use App\CoreDD\Domain\Stream\Entity\Stream;
use App\CoreDD\Domain\User\Entity\User;
use App\CoreDD\Infrastructure\Search\Elasticsearch\ElasticsearchRefresher;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePublisher implements MercurePublisherInterface
{
    public function __construct(
        private HubInterface $hub,
        private ElasticsearchRefresher $elasticsearchRefresher,
    ) {
    }

    public function refreshStream(Stream $stream, ?string $context = null): void
    {
        $this->elasticsearchRefresher->refreshStreamIndex();

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
        $this->elasticsearchRefresher->refreshNotificationIndex();

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

    public function refreshPlan(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'plan.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        if (false === $data) {
            return;
        }

        $update = new Update(
            "/users/{$user->getId()}/plan",
            $data
        );

        $this->hub->publish($update);
    }
}
