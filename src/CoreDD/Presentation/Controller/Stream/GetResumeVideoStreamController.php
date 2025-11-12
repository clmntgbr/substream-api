<?php

declare(strict_types=1);

namespace App\CoreDD\Presentation\Controller\Stream;

use App\CoreDD\Domain\Stream\Entity\Stream;
use App\CoreDD\Infrastructure\Storage\S3\S3StorageService;
use App\Exception\StreamNotDownloadableException;
use App\Util\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetResumeVideoStreamController extends AbstractController
{
    public function __construct(
        private S3StorageService $s3Service,
    ) {
    }

    public function __invoke(Stream $stream): BinaryFileResponse
    {
        if (!$stream->isResumeDownloadable()) {
            throw new StreamNotDownloadableException($stream->getId()->toRfc4122());
        }

        try {
            $resumePath = $this->s3Service->download($stream->getId(), '/'.$stream->getResumeFileName());

            $response = new BinaryFileResponse($resumePath);

            $originalFileName = $stream->getOriginalFileName();

            if (null === $originalFileName) {
                throw new \RuntimeException('file name is required');
            }

            $response->headers->set(
                'Content-Disposition',
                HeaderUtils::makeDisposition(
                    HeaderUtils::DISPOSITION_ATTACHMENT,
                    sprintf('%s.txt', Slugify::slug($originalFileName))
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
