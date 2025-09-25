<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Application\Command\UploadVideoCommand;
use App\Core\Domain\Aggregate\UploadVideoModel;
use App\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/streams', name: 'api_streams')]
class CreateStreamController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/video', name: 'video_create', methods: ['POST'])]
    public function createStreamByVideo(#[MapUploadedFile] UploadedFile $video, #[CurrentUser] User $user): Response
    {
        /** @var UploadVideoModel $uploadVideo */
        $uploadVideo = $this->commandBus->dispatch(new UploadVideoCommand($video));
        dump($uploadVideo);
        die;
        return Response::successResponse([]);
    }

    #[Route('/url', name: 'url_create', methods: ['POST'])]
    public function createStreamByUrl(#[CurrentUser] User $user): Response
    {
        return Response::successResponse([]);
    }
}