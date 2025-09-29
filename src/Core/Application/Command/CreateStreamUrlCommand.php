<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class CreateStreamUrlCommand implements SyncCommandInterface, TrackableCommandInterface
{
    use CommandIdTrait;

    private Uuid $streamId;

    public function __construct(
        public string $url,
        public User $user,
    ) {
        $this->streamId = Uuid::v4();
        $this->commandId = Uuid::v4();
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getData(): array
    {
        return [
            'streamId' => $this->getStreamId(),
        ];
    }

    public function supports(): bool
    {
        return true;
    }
}
