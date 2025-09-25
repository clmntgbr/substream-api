<?php

declare(strict_types=1);

namespace App\CQRS\Service;

use App\CQRS\Command\Stream\CreateStreamCommand;
use App\CQRS\Query\Stream\GetStreamQuery;
use App\Entity\Options;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

class CommandQueryFactory
{
    public function createStreamCommand(
        string $fileName,
        string $originalFileName,
        string $mimeType,
        int $size,
        string $url,
        User $user,
        Options $options
    ): CreateStreamCommand {
        return new CreateStreamCommand(
            $fileName,
            $originalFileName,
            $mimeType,
            $size,
            $url,
            $user,
            $options
        );
    }

    public function getStreamQuery(Uuid $streamId): GetStreamQuery
    {
        return new GetStreamQuery($streamId);
    }

    public function createOptionsFromData(array $data): Options
    {
        $options = new Options();
        
        $options->setSubtitleFont($data['subtitleFont'] ?? 'Arial');
        $options->setSubtitleSize($data['subtitleSize'] ?? 16);
        $options->setSubtitleColor($data['subtitleColor'] ?? '#FFFFFF');
        $options->setSubtitleBold($data['subtitleBold'] ?? false);
        $options->setSubtitleItalic($data['subtitleItalic'] ?? false);
        $options->setSubtitleUnderline($data['subtitleUnderline'] ?? false);
        $options->setSubtitleOutlineColor($data['subtitleOutlineColor'] ?? '#000000');
        $options->setSubtitleOutlineThickness($data['subtitleOutlineThickness'] ?? 2);
        $options->setSubtitleShadow($data['subtitleShadow'] ?? 1);
        $options->setSubtitleShadowColor($data['subtitleShadowColor'] ?? '#000000');
        $options->setVideoFormat($data['videoFormat'] ?? 'mp4');
        $options->setVideoParts($data['videoParts'] ?? 1);
        $options->setYAxisAlignment($data['yAxisAlignment'] ?? 0.0);
        
        return $options;
    }
}
