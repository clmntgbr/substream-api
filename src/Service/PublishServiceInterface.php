<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

interface PublishServiceInterface
{
    public function dispatchSearchStreams(User $user, ?string $context = null): void;
    public function dispatchSearchNotifications(User $user, ?string $context = null): void;
    public function refreshSearchStreams(User $user, ?string $context = null): void;
    public function refreshSearchNotifications(User $user, ?string $context = null): void;

}