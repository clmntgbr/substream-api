<?php

declare(strict_types=1);

namespace App\Service;

class StreamFileCleaner
{
    /**
     * @param array<string> $audioFiles
     *
     * @return array<int, string>
     */
    public function getCleanableFiles(array $audioFiles, ?string $subtitleAssFileName, ?string $resizeFileName, ?string $embedFileName): array
    {
        $audioFilesPaths = [];
        foreach ($audioFiles as $audioFile) {
            $audioFilesPaths[] = 'audios/'.$audioFile;
        }

        $files = $audioFilesPaths;

        if (null !== $subtitleAssFileName) {
            $files[] = 'subtitles/'.$subtitleAssFileName;
        }

        if (null !== $resizeFileName) {
            $files[] = $resizeFileName;
        }

        if (null !== $embedFileName) {
            $files[] = $embedFileName;
        }

        return $files;
    }
}
