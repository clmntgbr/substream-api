<?php

namespace App\Dto\Webhook;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class GetVideoSuccess
{
    public function __construct(
        #[SerializedName('file_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        private readonly string $fileName,
        #[SerializedName('original_file_name')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        private readonly string $originalFileName,
        #[SerializedName('mime_type')]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        private readonly string $mimeType,
        #[Assert\NotBlank]
        #[Assert\Positive]
        #[Assert\Type('integer')]
        private readonly int $size,
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $streamId,
        #[SerializedName('task_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $taskId,
        #[SerializedName('processing_time')]
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        private readonly int $processingTime,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }

    public function getProcessingTime(): int
    {
        return $this->processingTime;
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
