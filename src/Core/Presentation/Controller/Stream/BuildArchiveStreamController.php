<?php

declare(strict_types=1);

namespace App\Core\Presentation\Controller\Stream;

use App\Core\Domain\Stream\Entity\Stream;
use App\Exception\StreamNotDownloadableException;
use App\Service\BuildArchiveServiceInterface;
use App\Util\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class BuildArchiveStreamController extends AbstractController
{
    public function __construct(
        private BuildArchiveServiceInterface $buildArchiveService,
    ) {
    }

    public function __invoke(Stream $stream): BinaryFileResponse
    {
        if (!$stream->isDownloadable()) {
            throw new StreamNotDownloadableException($stream->getId()->toRfc4122());
        }

        try {
            $zip = $this->buildArchiveService->build($stream);

            $response = new BinaryFileResponse($zip->getPathname());

            $originalFileName = $stream->getOriginalFileName();
            $fileName = null !== $originalFileName ? Slugify::slug($originalFileName) : 'file';

            $response->headers->set(
                'Content-Disposition',
                HeaderUtils::makeDisposition(
                    HeaderUtils::DISPOSITION_ATTACHMENT,
                    sprintf('%s.zip', $fileName)
                )
            );

            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
