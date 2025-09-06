<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class GenerateSubtitlesResponse
{
    public function __construct(
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        public readonly string $streamId,
        #[SerializedName('subtitle_file')]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        public readonly string $subtitle,
        #[SerializedName('subtitle_files')]
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\NotBlank,
            new Assert\Type('string'),
            new Assert\Length(max: 255),
        ])]
        public readonly array $subtitleFiles,
    ) {
    }
}
