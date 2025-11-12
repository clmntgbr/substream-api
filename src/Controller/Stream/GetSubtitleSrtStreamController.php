<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Core\Domain\Stream\Entity\Stream;
use App\Exception\StreamNotDownloadableException;
use App\Service\S3ServiceInterface;
use App\Util\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetSubtitleSrtStreamController extends AbstractController
{
    public function __construct(
        private S3ServiceInterface $s3Service,
    ) {
    }

    public function __invoke(Stream $stream): BinaryFileResponse
    {
        if (!$stream->isSrtDownloadable()) {
            throw new StreamNotDownloadableException($stream->getId()->toRfc4122());
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
