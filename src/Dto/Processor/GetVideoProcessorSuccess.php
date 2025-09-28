<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class GetVideoProcessorSuccess
{
    public function __construct(
        #[SerializedName('file_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        public readonly string $fileName,
        #[SerializedName('original_file_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        public readonly string $originalFileName,
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

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
