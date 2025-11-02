<?php

declare(strict_types=1);

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ChunkVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private int $chunkNumber,
        private string $embedFileName,
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

    public function getChunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function getEmbedFileName(): string
    {
        return $this->embedFileName;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
            'stream_id' => (string) $this->streamId,
            'chunk_number' => $this->chunkNumber,
            'embed_file_name' => $this->embedFileName,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.chunk_video'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/chunkvideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/chunkvideofailure';
    }
}
