<?php

declare(strict_types=1);

namespace App\Dto\Webhook;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class GenerateSubtitleSuccess
{
    public function __construct(
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
        #[SerializedName('subtitle_srt_file_name')]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(max: 255)]
        private readonly string $subtitleSrtFileName,
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

    public function getSubtitleSrtFileName(): string
    {
        return $this->subtitleSrtFileName;
    }
}
