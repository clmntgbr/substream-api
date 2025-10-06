<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class TransformSubtitleMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleSrtFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleSrtFileName(): string
    {
        return $this->subtitleSrtFileName;
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'subtitle_srt_file_name' => $this->subtitleSrtFileName,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.transform_subtitle'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/transformsubtitlesuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/transformsubtitlefailure';
    }
}
