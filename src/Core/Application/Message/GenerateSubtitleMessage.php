<?php

namespace App\Core\Application\Message;

use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class GenerateSubtitleMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $streamId,
        private array $audioFiles,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->streamId,
            'audio_files' => $this->audioFiles,
        ];
    }

    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [
            new AmqpStamp('core.generate_subtitle'),
        ];
    }

    public function getWebhookUrlSuccess(): string
    {
        return 'webhook/generatesubtitlesuccess';
    }

    public function getWebhookUrlFailure(): string
    {
        return 'webhook/generatesubtitlefailure';
    }
}
