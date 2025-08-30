<?php

namespace App\Controller;

use App\Application\Command\UploadVideoCommand;
use App\Exception\UploadVideoException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

#[Route('/api/upload', name: 'api_upload_')]
class UploadVideoController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/video', name: 'video', methods: ['POST'])]
    public function uploadVideo(#[MapUploadedFile] UploadedFile $video): JsonResponse {
        try {
            $this->messageBus->dispatch(new UploadVideoCommand($video));
            return new JsonResponse(['message' => 'Video uploaded']);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
