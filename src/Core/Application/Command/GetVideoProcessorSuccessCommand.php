<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorSuccessCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    use JobCommandTrait;

    public function __construct(
        public Uuid $streamId,
        public readonly string $fileName,
        public readonly string $originalFileName,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
    }

    public function getData(): array
    {
        return [
            'streamId' => $this->streamId,
        ];
    }

    public function supports(): bool
    {
        return false;
    }
}
