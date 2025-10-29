<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class PublishService implements PublishServiceInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function refreshSearchStreams()
    {
        $update = new Update(
            '/search/streams',
            json_encode([
                'type' => 'streams.refresh',
            ])
        );

        $this->hub->publish($update);
    }
}