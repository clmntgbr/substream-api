<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Uid\Uuid;

final class UpdateSearchStreamsCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $userId,
        private ?string $context = null,
    ) {
    }
    
    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new DelayStamp(2000),
        ];
    }
}
