<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Uid\Uuid;

final class GenerateSubtitleCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private array $audioFiles,
        private string $language,
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

    public function getLanguage(): string
    {
        return $this->language;
    }
}
