<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Uid\Uuid;

final class DeleteStreamAfter14DaysCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getStamps(): array
    {
        return [
            new DelayStamp(14 * 24 * 60 * 60 * 1000),
        ];
    }
}
