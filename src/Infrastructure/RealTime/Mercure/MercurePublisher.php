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

        $this->publish($user, $data);
    }

    public function refreshUser(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'user.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    public function refreshPlan(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'plan.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    public function refreshSubscription(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'subscription.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    public function refreshStreams(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'streams.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    public function refreshNotifications(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'notifications.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    public function refreshPlans(User $user, ?string $context = null): void
    {
        $data = json_encode([
            'type' => 'plans.refresh',
            'userId' => $user->getId(),
            'context' => $context,
        ]);

        $this->publish($user, $data);
    }

    private function publish(User $user, string $data): void
    {
        $update = new Update(
            "/users/{$user->getId()}",
            $data
        );

        $this->hub->publish($update);
    }
}
