<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Stream\Download;

use App\Domain\Stream\Entity\Stream;
use App\Infrastructure\Stream\Archive\BuildArchiveServiceInterface;
use App\Shared\Utils\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetArchiveStreamController extends AbstractController
{
    public function __construct(
        private BuildArchiveServiceInterface $buildArchiveService,
    ) {
    }

    public function __invoke(Stream $stream): BinaryFileResponse
    {
        if (!$stream->isDownloadable()) {
            throw new \Exception($stream->getId()->toRfc4122());
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
