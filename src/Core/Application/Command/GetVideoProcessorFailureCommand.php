<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class GetVideoProcessorFailureCommand implements AsyncCommandInterface, TrackableCommandInterface
{
    use JobCommandTrait;

    public function __construct(
        public Uuid $streamId,
        public readonly ?string $errorMessage = null,
    ) {
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
