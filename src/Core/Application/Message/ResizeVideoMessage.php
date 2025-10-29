<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ResizeVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private string $fileName,
        private string $format,
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

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
            'stream_id' => (string) $this->streamId,
            'file_name' => $this->fileName,
            'format' => $this->format,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.resize_video'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/resizevideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/resizevideofailure';
    }
}
