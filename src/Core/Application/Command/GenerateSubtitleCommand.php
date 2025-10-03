<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class GenerateSubtitleCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private array $audioFiles,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }
}
