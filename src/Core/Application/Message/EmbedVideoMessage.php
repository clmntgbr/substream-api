<?php

declare(strict_types=1);

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class EmbedVideoMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private string $subtitleAssFileName,
        private string $resizeFileName,
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

    public function getSubtitleAssFileName(): string
    {
        return $this->subtitleAssFileName;
    }

    public function getResizeFileName(): string
    {
        return $this->resizeFileName;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
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
