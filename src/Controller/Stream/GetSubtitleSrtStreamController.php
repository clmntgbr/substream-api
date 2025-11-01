<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Entity\Stream;
use App\Exception\StreamNotDownloadableException;
use App\Service\S3ServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Util\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetSubtitleSrtStreamController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private S3ServiceInterface $s3Service,
    ) {
    }

    public function __invoke(Stream $stream)
    {
        if (!$stream->isSrtDownloadable()) {
            throw new StreamNotDownloadableException($stream->getId()->toRfc4122());
        }

        try {
            $srtPath = $this->s3Service->download($stream->getId(), 'subtitles/'.$stream->getSubtitleSrtFileName());

            $response = new BinaryFileResponse($srtPath);

            $response->headers->set(
                'Content-Disposition',
                HeaderUtils::makeDisposition(
                    HeaderUtils::DISPOSITION_ATTACHMENT,
                    sprintf('%s.srt', Slugify::slug($stream->getOriginalFileName()))
                ),
                'text/plain'
            );

            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $e) {
            // Exception will be caught by BusinessExceptionListener
            throw $e;
        }
    }
}
