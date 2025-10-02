<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class GetVideoCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private User $user,
        private string $url,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
