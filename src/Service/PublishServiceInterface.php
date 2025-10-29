<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

interface PublishServiceInterface
{
    public function refreshSearchStreams(User $user): void;
}