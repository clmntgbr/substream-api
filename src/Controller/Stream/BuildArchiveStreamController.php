<?php

namespace App\Controller\Stream;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Dto\CreateStreamOption;
use App\Dto\CreateStreamUrl;
use App\Dto\CreateStreamUrlPayload;
use App\Entity\Stream;
use App\Entity\User;
use App\Exception\StreamNotDownloadableException;
use App\Service\BuildArchiveServiceInterface;
use App\Service\DownloadStreamServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
use App\Util\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class BuildArchiveStreamController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private BuildArchiveServiceInterface $buildArchiveService,
    ) {
    }

    public function __invoke(Stream $stream) 
    {
        if (!$stream->isDownloadable()) {
            throw new StreamNotDownloadableException();
        }

        $zip = $this->buildArchiveService->build($stream);
        
        $response = new BinaryFileResponse($zip->getPathname());

        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                sprintf('%s.zip', Slugify::slug($stream->getOriginalFileName()))
            )
        );

        $response->deleteFileAfterSend();

        return $response;
    }
}
