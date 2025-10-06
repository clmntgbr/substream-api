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
        private string $resizeFileName,
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

    public function getResizeFileName(): string
    {
        return $this->resizeFileName;
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'subtitle_ass_file_name' => $this->subtitleAssFileName,
            'resize_file_name' => $this->resizeFileName,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.embed_video'),
        ];
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
