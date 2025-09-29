<?php

declare(strict_types=1);

namespace App\Shared\Application\Middleware;

use Symfony\Component\Uid\Uuid;

interface TrackableCommandInterface
{
    public function getCommandId(): Uuid;

    public function setCommandId(Uuid $commandId): self;

    public function getData(): array;

    public function supports(): bool;
}
