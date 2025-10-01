<?php

namespace App\Core\Application\Command\Async;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundSuccessCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private array $audioFiles,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }
}
