<?php

namespace App\Service;

use App\Entity\Stream;
use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

interface PublishServiceInterface
{
    public function refreshStream(Stream $stream, ?string $context = null): void;
    public function refreshSearchStreams(Stream $stream, ?string $context = null): void;
    public function refreshSearchNotifications(User $user, ?string $context = null): void;
}