<?php

declare(strict_types=1);

namespace App\Service;

class StreamFileCleaner
{
    /**
     * @param array<string> $audioFiles
     */
    public function getCleanableFiles(array $audioFiles, ?string $subtitleAssFileName, ?string $resizeFileName, ?string $embedFileName): array
    {
        $audioFilesPaths = [];
        foreach ($audioFiles as $audioFile) {
            $audioFilesPaths[] = 'audios/'.$audioFile;
        }

        return [
            ...$audioFilesPaths,
            'subtitles/'.$subtitleAssFileName,
            $resizeFileName,
            $embedFileName,
        ];
    }
}
