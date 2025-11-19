<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class GenerateSubtitleCommand implements AsynchronousInterface
{
    /**
     * @param array<int, string> $audioFiles
     */
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

    /**
     * @return array<int, string>
     */
    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getStamps(): array
    {
        return [];
    }
}
