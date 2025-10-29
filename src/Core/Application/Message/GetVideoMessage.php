<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class GetVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private string $url,
    ) {
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
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
            'task_id' => (string) $this->taskId,
            'stream_id' => (string) $this->streamId,
            'url' => $this->url,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.get_video'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/getvideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/getvideofailure';
    }
}
