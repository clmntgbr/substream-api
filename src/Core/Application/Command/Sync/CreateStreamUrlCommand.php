<?php

namespace App\Core\Application\Command\Sync;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamUrlCommand implements SyncCommandInterface
{
    private Uuid $streamId;

    public function __construct(
        private string $url,
        private User $user,
    ) {
        $this->streamId = Uuid::v4();
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
}
