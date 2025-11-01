<?php

declare(strict_types=1);

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
                'subtitle_font' => $this->option->getSubtitleFont(),
                'subtitle_size' => $this->option->getSubtitleSize(),
                'subtitle_color' => $this->option->getSubtitleColor(),
                'subtitle_bold' => $this->option->getSubtitleBold(),
                'subtitle_italic' => $this->option->getSubtitleItalic(),
                'subtitle_underline' => $this->option->getSubtitleUnderline(),
                'subtitle_outline_color' => $this->option->getSubtitleOutlineColor(),
                'subtitle_outline_thickness' => $this->option->getSubtitleOutlineThickness(),
                'subtitle_shadow' => $this->option->getSubtitleShadow(),
                'subtitle_shadow_color' => $this->option->getSubtitleShadowColor(),
                'y_axis_alignment' => $this->option->getYAxisAlignment(),
            ],
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
