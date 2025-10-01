<?php

namespace App\Core\Application\Command\Async;

use App\Shared\Application\Command\AsyncCommandInterface;
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
