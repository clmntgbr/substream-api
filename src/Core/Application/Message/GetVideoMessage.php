<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class GetVideoMessage implements AsyncMessageInterface
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

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'url' => $this->url,
        ];
    }

    public function getRoutingKey(): AmqpStamp
    {
        return new AmqpStamp('core.get_video');
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'getvideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'getvideofailure';
    }
}
