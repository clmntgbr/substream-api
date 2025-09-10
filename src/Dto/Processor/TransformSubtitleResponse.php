<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class TransformSubtitleResponse
{
    public function __construct(
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        public readonly string $streamId,
        #[SerializedName('subtitle_ass_file')]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        public readonly string $subtitleAssFile,
    ) {
    }
}
