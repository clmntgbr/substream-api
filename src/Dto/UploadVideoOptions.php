<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\SubtitleFontEnum;
use App\Enum\VideoFormatEnum;
use Symfony\Component\Validator\Constraints as Assert;

class UploadVideoOptions
{
    public function __construct(
        #[Assert\Length(max: 100)]
        #[Assert\Choice(choices: [SubtitleFontEnum::ARIAL->value, SubtitleFontEnum::TIMES_NEW_ROMAN->value, SubtitleFontEnum::COURIER_NEW->value], message: 'Invalid subtitle font')]
        public readonly string $subtitleFont = SubtitleFontEnum::ARIAL->value,
        #[Assert\Positive]
        #[Assert\Range(min: 8, max: 72)]
        public readonly int $subtitleSize = 16,
        #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/', message: 'Invalid subtitle color')]
        public readonly string $subtitleColor = '#FFFFFF',
        #[Assert\NotNull]
        #[Assert\Type('bool')]
        public readonly bool $subtitleBold = false,
        #[Assert\NotNull]
        #[Assert\Type('bool')]
        public readonly bool $subtitleItalic = false,
        #[Assert\NotNull]
        #[Assert\Type('bool')]
        public readonly bool $subtitleUnderline = false,
        #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/', message: 'Invalid subtitle outline color')]
        public readonly string $subtitleOutlineColor = '#000000',
        #[Assert\PositiveOrZero]
        #[Assert\Range(min: 0, max: 10)]
        public readonly int $subtitleOutlineThickness = 0,
        #[Assert\PositiveOrZero]
        #[Assert\Range(min: 0, max: 5)]
        public readonly int $subtitleShadow = 0,
        #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/', message: 'Invalid subtitle shadow color')]
        public readonly string $subtitleShadowColor = '#000000',
        #[Assert\Length(max: 20)]
        #[Assert\Choice(choices: [VideoFormatEnum::ORIGINAL->value, VideoFormatEnum::ZOOMED_916->value, VideoFormatEnum::NORMAL_916_WITH_BORDERS->value, VideoFormatEnum::DUPLICATED_BLURRED_916->value], message: 'Invalid video format')]
        public readonly string $videoFormat = VideoFormatEnum::ORIGINAL->value,
        #[Assert\Positive]
        public readonly int $videoParts = 1,
        #[Assert\PositiveOrZero]
        public readonly float $yAxisAlignment = 0.0,
    ) {
    }
}
