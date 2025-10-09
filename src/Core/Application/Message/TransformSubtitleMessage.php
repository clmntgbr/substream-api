<?php

namespace App\Core\Application\Message;

use App\Entity\Option;
use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Uid\Uuid;

readonly class TransformSubtitleMessage implements AsyncMessageInterface
{
    public function __construct(
        private Uuid $taskId,
        private Uuid $streamId,
        private Option $option,
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

    public function getOption(): Option
    {
        return $this->option;
    }

    public function jsonSerialize(): array
    {
        return [
            'task_id' => (string) $this->taskId,
            'stream_id' => (string) $this->streamId,
            'subtitle_srt_file_name' => $this->subtitleSrtFileName,
            'option' => [
                "subtitleFont" => $this->option->getSubtitleFont(),
                "subtitleSize" => $this->option->getSubtitleSize(),
                "subtitleColor" => $this->option->getSubtitleColor(),
                "subtitleBold" => $this->option->getSubtitleBold(),
                "subtitleItalic" => $this->option->getSubtitleItalic(),
                "subtitleUnderline" => $this->option->getSubtitleUnderline(),
                "subtitleOutlineColor" => $this->option->getSubtitleOutlineColor(),
                "subtitleOutlineThickness" => $this->option->getSubtitleOutlineThickness(),
                "subtitleShadow" => $this->option->getSubtitleShadow(),
                "subtitleShadowColor" => $this->option->getSubtitleShadowColor(),
                "yAxisAlignment" => $this->option->getYAxisAlignment(),
            ]
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
