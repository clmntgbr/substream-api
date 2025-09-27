<?php

declare(strict_types=1);

namespace App\Shared\Application\Middleware;

interface TrackableCommandInterface
{
    public function getIdentifier(): ?string;

    public function supports(): bool;
}
