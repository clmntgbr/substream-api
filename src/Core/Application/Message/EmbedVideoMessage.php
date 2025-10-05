<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class EmbedVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleAssFileName,
        private string $resizedFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleAssFileName(): string
    {
        return $this->subtitleAssFileName;
    }

    public function getResizedFileName(): string
    {
        return $this->resizedFileName;
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'subtitle_ass_file_name' => $this->subtitleAssFileName,
            'resized_file_name' => $this->resizedFileName,
        ];
    }

    public function getRoutingKey(): AmqpStamp
    {
        return new AmqpStamp('core.embed_video');
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/embedvideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/embedvideofailure';
    }
}
