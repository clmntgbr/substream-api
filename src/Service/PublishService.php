<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PublishService implements PublishServiceInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
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