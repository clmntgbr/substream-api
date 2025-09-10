<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class TransformSubtitle implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->stream->getId(),
            'subtitle_srt_file' => $this->stream->getSubtitleSrtFile(),
            'options' => [
                'subtitle_font' => $this->stream->getOptions()->getSubtitleFont(),
                'subtitle_size' => $this->stream->getOptions()->getSubtitleSize(),
                'subtitle_color' => $this->stream->getOptions()->getSubtitleColor(),
                'subtitle_bold' => $this->stream->getOptions()->getSubtitleBold(),
                'subtitle_italic' => $this->stream->getOptions()->getSubtitleItalic(),
                'subtitle_underline' => $this->stream->getOptions()->getSubtitleUnderline(),
                'subtitle_outline_color' => $this->stream->getOptions()->getSubtitleOutlineColor(),
                'subtitle_outline_thickness' => $this->stream->getOptions()->getSubtitleOutlineThickness(),
                'subtitle_shadow' => $this->stream->getOptions()->getSubtitleShadow(),
                'subtitle_shadow_color' => $this->stream->getOptions()->getSubtitleShadowColor(),
                'y_axis_alignment' => $this->stream->getOptions()->getYAxisAlignment(),
            ],
        ];
    }
}
