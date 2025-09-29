<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use Symfony\Component\Uid\Uuid;

trait CommandIdTrait
{
    private ?Uuid $commandId = null;

    public function getCommandId(): Uuid
    {
        return $this->commandId;
    }

    public function setCommandId(Uuid $commandId): self
    {
        $this->commandId = $commandId;

        return $this;
    }
}
