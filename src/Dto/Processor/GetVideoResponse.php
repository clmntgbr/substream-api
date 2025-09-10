<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class GetVideoResponse
{
    public function __construct(
        #[SerializedName('file_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        public readonly string $videoFileName,
        #[SerializedName('original_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        public readonly string $originalName,
        #[SerializedName('mime_type')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        public readonly string $mimeType,
        #[SerializedName('size')]
        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Assert\Type('integer')]
        public readonly int $size,
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        public readonly string $streamId,
    ) {
    }
}
