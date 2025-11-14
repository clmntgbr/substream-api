<?php

declare(strict_types=1);

namespace App\Infrastructure\RealTime\Mercure;

use App\Domain\Stream\Entity\Stream;
use App\Domain\User\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use function Safe\json_encode;

class MercurePublisher implements MercurePublisherInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function refreshStream(Stream $stream, ?string $context = null): void
    {
        $user = $stream->getUser();

        $data = json_encode([
            'type' => 'stream.refresh',
            'userId' => $user->getId(),
            'streamId' => $stream->getId(),
            'context' => $context,
        ]);

        $this->publish("/users/{$user->getId()}", $data);
    }

    public function refreshStreams(Stream $stream, ?string $context = null): void
    {
        $user = $stream->getUser();

        $data = json_encode([
            'type' => 'streams.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish("/users/{$user->getId()}", $data);
    }

    public function refreshNotifications(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'notifications.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish("/users/{$user->getId()}", $data);
    }

    public function refreshPlans(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'plans.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish("/users/{$user->getId()}", $data);
    }

    private function publish(string $topic, string $data): void
    {
        $update = new Update(
            $topic,
            $data
        );

        $this->hub->publish($update);
    }
}
