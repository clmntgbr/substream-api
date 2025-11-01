<?php

declare(strict_types=1);

namespace App\Dto\Webhook;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ExtractSoundSuccess
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
        #[SerializedName('duration')]
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        private readonly int $duration,
        #[SerializedName('audio_files')]
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(max: 255),
        ])]
        private readonly array $audioFiles,
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

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}
