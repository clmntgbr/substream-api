<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use App\Enum\StreamStatusEnum;

readonly class StreamStatus
{
    public function __construct(
        private string $status,
        private array $statuses,
    ) {
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, [
            StreamStatusEnum::UPLOADED->value,
            StreamStatusEnum::CREATED->value,
            StreamStatusEnum::UPLOADING->value,
            StreamStatusEnum::EXTRACTING_SOUND->value,
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value,
            StreamStatusEnum::GENERATING_SUBTITLE->value,
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::RESIZING_VIDEO->value,
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED->value,
            StreamStatusEnum::EMBEDDING_VIDEO->value,
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED->value,
            StreamStatusEnum::CHUNKING_VIDEO->value,
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED->value,
            StreamStatusEnum::RESUMING->value,
            StreamStatusEnum::RESUMING_COMPLETED->value,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === StreamStatusEnum::COMPLETED->value;
    }

    public function isFailed(): bool
    {
        return in_array($this->status, [
            StreamStatusEnum::FAILED->value,
            StreamStatusEnum::UPLOAD_FAILED->value,
            StreamStatusEnum::EXTRACTING_SOUND_FAILED->value,
            StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED->value,
            StreamStatusEnum::RESIZING_VIDEO_FAILED->value,
            StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value,
            StreamStatusEnum::CHUNKING_VIDEO_FAILED->value,
            StreamStatusEnum::RESUMING_FAILED->value,
        ]);
    }

    public function getProgress(): int
    {
        $statusEnum = StreamStatusEnum::from($this->status);

        if (str_contains($this->status, 'failed')) {
            return 100;
        }

        return match ($statusEnum) {
            StreamStatusEnum::CREATED => 0,
            StreamStatusEnum::UPLOADING => 5,
            StreamStatusEnum::UPLOADED => 10,
            StreamStatusEnum::EXTRACTING_SOUND => 20,
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED => 30,
            StreamStatusEnum::GENERATING_SUBTITLE => 40,
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED => 50,
            StreamStatusEnum::TRANSFORMING_SUBTITLE => 60,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED => 70,
            StreamStatusEnum::RESIZING_VIDEO => 75,
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED => 80,
            StreamStatusEnum::EMBEDDING_VIDEO => 85,
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED => 88,
            StreamStatusEnum::CHUNKING_VIDEO => 92,
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED => 94,
            StreamStatusEnum::RESUMING => 96,
            StreamStatusEnum::RESUMING_COMPLETED => 98,
            StreamStatusEnum::COMPLETED,
            StreamStatusEnum::DELETED => 100,
            default => 0,
        };
    }

    public function isDownloadable(): bool
    {
        return in_array(StreamStatusEnum::COMPLETED->value, $this->statuses);
    }

    public function isSrtDownloadable(?string $subtitleSrtFileName): bool
    {
        return in_array(StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value, $this->statuses) && null !== $subtitleSrtFileName;
    }

    public function isResumeDownloadable(?string $resumeFileName): bool
    {
        return in_array(StreamStatusEnum::RESUMING_COMPLETED->value, $this->statuses) && null !== $resumeFileName;
    }

    public function getFilterStatus(): ?string
    {
        return match ($this->status) {
            StreamStatusEnum::CREATED->value => 'created',
            StreamStatusEnum::UPLOADING->value => 'uploading',
            StreamStatusEnum::UPLOADED->value => 'uploaded',
            StreamStatusEnum::EXTRACTING_SOUND->value => 'extracting_sound',
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value => 'extracting_sound_completed',
            StreamStatusEnum::GENERATING_SUBTITLE->value => 'generating_subtitle',
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value => 'generating_subtitle_completed',
            StreamStatusEnum::TRANSFORMING_SUBTITLE->value => 'transforming_subtitle',
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED->value => 'transforming_subtitle_completed',
            StreamStatusEnum::RESIZING_VIDEO->value => 'resizing_video',
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED->value => 'resizing_video_completed',
            StreamStatusEnum::EMBEDDING_VIDEO->value => 'embedding_video',
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED->value => 'embedding_video_completed',
            StreamStatusEnum::CHUNKING_VIDEO->value => 'chunking_video',
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED->value => 'chunking_video_completed',
            StreamStatusEnum::RESUMING->value => 'resuming',
            StreamStatusEnum::RESUMING_COMPLETED->value => 'resuming_completed',
            StreamStatusEnum::COMPLETED->value => 'completed',
            StreamStatusEnum::DELETED->value => 'deleted',

            StreamStatusEnum::FAILED->value => 'failed',
            StreamStatusEnum::UPLOAD_FAILED->value => 'failed',
            StreamStatusEnum::EXTRACTING_SOUND_FAILED->value => 'failed',
            StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value => 'failed',
            StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED->value => 'failed',
            StreamStatusEnum::RESIZING_VIDEO_FAILED->value => 'failed',
            StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value => 'failed',
            StreamStatusEnum::CHUNKING_VIDEO_FAILED->value => 'failed',
            StreamStatusEnum::RESUMING_FAILED->value => 'failed',
        };
    }
}
