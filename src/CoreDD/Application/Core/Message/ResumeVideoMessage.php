<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Core\Message;

use App\Shared\Application\Message\AsynchronousMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ResumeVideoMessage implements AsynchronousMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private string $subtitleSrtFileName,
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

    public function getSubtitleSrtFileName(): string
    {
        return $this->subtitleSrtFileName;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
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
            new AmqpStamp('core.resume_video'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/resumevideosuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/resumevideofailure';
    }
}
