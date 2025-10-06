<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandAbstract;
use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class ExtractSoundCommand extends AsyncCommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
