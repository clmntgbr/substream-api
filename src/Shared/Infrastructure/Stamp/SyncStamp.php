<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class SyncStamp implements StampInterface
{
    public function __construct()
    {
    }
}
