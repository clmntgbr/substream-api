<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ChunkVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $streamId,
        private int $chunkNumber,
        private string $embedFileName,
    ) {
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

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'chunk_number' => $this->chunkNumber,
            'embed_file_name' => $this->embedFileName,
        ];
    }

    public function getRoutingKey(): AmqpStamp
    {
        return new AmqpStamp('core.chunk_video');
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
