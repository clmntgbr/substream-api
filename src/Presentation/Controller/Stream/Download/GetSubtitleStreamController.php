<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Stream\Download;

use App\Domain\Stream\Entity\Stream;
use App\Infrastructure\Storage\S3\S3StorageService;
use App\Shared\Utils\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetSubtitleStreamController extends AbstractController
{
    public function __construct(
        private S3StorageService $s3Service,
    ) {
    }

    public function __invoke(Stream $stream): BinaryFileResponse
    {
        if (!$stream->isSrtDownloadable()) {
            throw new \Exception('Stream not downloadable');
        }

        try {
            $srtPath = $this->s3Service->download($stream->getId(), 'subtitles/'.$stream->getSubtitleSrtFileName());

            $response = new BinaryFileResponse($srtPath);

            $originalFileName = $stream->getOriginalFileName();

            if (null === $originalFileName) {
                throw new \RuntimeException('file name is required');
            }

            $response->headers->set(
                'Content-Disposition',
                HeaderUtils::makeDisposition(
                    HeaderUtils::DISPOSITION_ATTACHMENT,
                    sprintf('%s.srt', Slugify::slug($originalFileName))
                )
            );

            $response->headers->set('Content-Type', 'text/plain');

            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
