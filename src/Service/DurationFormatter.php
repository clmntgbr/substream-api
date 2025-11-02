<?php

declare(strict_types=1);

namespace App\Service;

class DurationFormatter
{
    public function format(?int $milliseconds): ?string
    {
        if (null === $milliseconds) {
            return null;
        }

        $totalSeconds = (int) floor($milliseconds / 1000);
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if (0 === $hours && 0 === $minutes && 0 === $seconds) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function formatFromSeconds(?int $seconds): ?string
    {
        if (null === $seconds) {
            return null;
        }

        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
