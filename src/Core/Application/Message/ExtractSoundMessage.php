<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function jsonSerialize(): array
    {
        return [
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
