<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateStreamUrlCommand implements SyncCommandInterface
{
    private Uuid $streamId;

    public function __construct(
        private string $name,
        private string $url,
        private string $thumbnailUrl,
        private Uuid $optionId,
        private User $user,
    ) {
        $this->streamId = Uuid::v4();
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
