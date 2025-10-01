<?php

namespace App\Core\Application\Command\Async;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
