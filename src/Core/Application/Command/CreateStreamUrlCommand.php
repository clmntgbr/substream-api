<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\SyncCommandInterface;

class CreateStreamUrlCommand implements SyncCommandInterface
{
    public function __construct(
        public string $url,
    ) {
    }
}
