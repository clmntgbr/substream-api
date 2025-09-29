<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorSuccessCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    use CommandIdTrait;

    public function __construct(
        public Uuid $streamId,
        public readonly string $fileName,
        public readonly string $originalFileName,
        public readonly string $mimeType,
        public readonly int $size,
    ) {
        $this->commandId = Uuid::v4();
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
