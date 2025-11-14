<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

final class GetVideoCommand implements AsynchronousInterface
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

    /**
     * @return array<int, AmqpStamp>
     */
    public function getStamps(): array
    {
        return [];
    }
}
