<?php

namespace App\Service;

use App\Enum\StreamStatusEnum;

class ProcessingTimeEstimator
{
    private const REFERENCE_SIZE_MB = 400;

    private const BASE_TIMINGS = [
        'GetVideoCommand' => 60,
        'ExtractSoundCommand' => 5,
        'GenerateSubtitleCommand' => 32,
        'TransformSubtitleCommand' => 0,
        'ResizeVideoCommand' => 76,
        'EmbedVideoCommand' => 80,
        'ChunkVideoCommand' => 23,
    ];

    // Mapping entre les status et les commandes
    private const STATUS_TO_COMMAND = [
        'uploading' => 'GetVideoCommand',
        'extracting_sound' => 'ExtractSoundCommand',
        'generating_subtitle' => 'GenerateSubtitleCommand',
        'transforming_subtitle' => 'TransformSubtitleCommand',
        'resizing_video' => 'ResizeVideoCommand',
        'embedding_video' => 'EmbedVideoCommand',
        'chunking_video' => 'ChunkVideoCommand',
    ];

    // Ordre des étapes de traitement
    private const PROCESSING_ORDER = [
        'uploading',
        'uploaded',
        'extracting_sound',
        'extracting_sound_completed',
        'generating_subtitle',
        'generating_subtitle_completed',
        'transforming_subtitle',
        'transforming_subtitle_completed',
        'resizing_video',
        'resizing_video_completed',
        'embedding_video',
        'embedding_video_completed',
        'chunking_video',
        'chunking_video_completed',
    ];

    public static function estimateRemainingTime(StreamStatusEnum $currentStatus, float $videoSizeMB, bool $hasUrl = false): int
    {
        // If failed or completed, no remaining time
        if (self::isFailedStatus($currentStatus) || StreamStatusEnum::COMPLETED === $currentStatus) {
            return 0;
        }

        $scaleFactor = $videoSizeMB / self::REFERENCE_SIZE_MB;
        $remainingSeconds = 0;

        // Get current position in processing order
        $currentIndex = array_search($currentStatus->value, self::PROCESSING_ORDER);

        if (false === $currentIndex) {
            return 0;
        }

        // Iterate through all processing steps
        foreach (self::STATUS_TO_COMMAND as $statusValue => $command) {
            // Skip GetVideoCommand if the stream doesn't have a URL
            if ('GetVideoCommand' === $command && !$hasUrl) {
                continue;
            }

            $processingStatusIndex = array_search($statusValue, self::PROCESSING_ORDER);
            $completedStatusValue = $statusValue.'_completed';
            $completedStatusIndex = array_search($completedStatusValue, self::PROCESSING_ORDER);

            // If we're currently processing this step, include full time
            if ($statusValue === $currentStatus->value) {
                $remainingSeconds += self::calculateEstimatedTime($command, $scaleFactor);
            }
            // If this step hasn't been started yet (we're before the processing status)
            elseif (false !== $processingStatusIndex && $currentIndex < $processingStatusIndex) {
                $remainingSeconds += self::calculateEstimatedTime($command, $scaleFactor);
            }
            // If we're between processing and completed (shouldn't happen but handle it)
            elseif (false !== $processingStatusIndex && false !== $completedStatusIndex
                    && $currentIndex > $processingStatusIndex && $currentIndex < $completedStatusIndex) {
                // Still add the time as we're not completed yet
                $remainingSeconds += self::calculateEstimatedTime($command, $scaleFactor);
            }
        }

        // Add 20% margin
        return (int) round($remainingSeconds * 1.2);
    }

    /**
     * Calcule le temps estimé pour une commande.
     */
    private static function calculateEstimatedTime(string $command, float $scaleFactor): int
    {
        $baseTime = self::BASE_TIMINGS[$command];

        // TransformSubtitleCommand reste constant (traitement de texte)
        if ('TransformSubtitleCommand' === $command) {
            return $baseTime;
        }

        // Les autres commandes sont impactées par la taille de la vidéo
        return (int) round($baseTime * $scaleFactor);
    }

    private static function isFailedStatus(StreamStatusEnum $status): bool
    {
        return in_array($status, [
            StreamStatusEnum::UPLOAD_FAILED,
            StreamStatusEnum::EXTRACTING_SOUND_FAILED,
            StreamStatusEnum::GENERATING_SUBTITLE_FAILED,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED,
            StreamStatusEnum::RESIZING_VIDEO_FAILED,
            StreamStatusEnum::EMBEDDING_VIDEO_FAILED,
            StreamStatusEnum::CHUNKING_VIDEO_FAILED,
        ]);
    }
}
