<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class GetVideoCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $url,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
