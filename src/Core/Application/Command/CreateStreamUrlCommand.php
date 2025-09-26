<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;

class CreateStreamUrlCommand implements SyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public string $url,
    ) {
    }
}
