<?php

declare(strict_types=1);

namespace App\Core\Application\Core\Message;

use App\Shared\Application\Message\AsynchronousMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundMessage implements AsynchronousMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private string $fileName,
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
            'stream_id' => (string) $this->streamId,
            'file_name' => $this->fileName,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.extract_sound'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/extractsoundsuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/extractsoundfailure';
    }
}
