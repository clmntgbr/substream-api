<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorFailureCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    use CommandIdTrait;

    public function __construct(
        public Uuid $streamId,
        public readonly ?string $errorMessage = null,
    ) {
        $this->commandId = Uuid::v4();
    }

    public function getData(): array
    {
        return [
            'streamId' => $this->streamId,
        ];
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function supports(): bool
    {
        return false;
    }
}
