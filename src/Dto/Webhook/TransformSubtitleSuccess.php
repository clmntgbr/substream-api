<?php

namespace App\Dto\Webhook;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TransformSubtitleSuccess
{
    public function __construct(
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $streamId,
        #[SerializedName('subtitle_ass_file_name')]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        private readonly string $subtitleAssFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleAssFileName(): string
    {
        return $this->subtitleAssFileName;
    }
}
