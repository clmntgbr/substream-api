<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

interface PublishServiceInterface
{
    public function refreshSearchStreams();
}