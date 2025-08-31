<?php

namespace App\Controller;

use App\Application\Command\CreateStreamByUrlCommand;
use App\Application\Command\CreateStreamCommand;
use App\Application\Command\UploadVideoByUrlCommand;
use App\Application\Command\UploadVideoCommand;
use App\Dto\UploadVideoByUrl;
use App\Entity\User;
use App\Exception\InvalidVideoMimeTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/upload', name: 'api_upload_')]
class UploadVideoController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/video', name: 'video', methods: ['POST'])]
    public function uploadVideo(#[MapUploadedFile] UploadedFile $video, #[CurrentUser] User $user): JsonResponse
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($video, $constraints);

        if (count($violations) > 0) {
            throw new InvalidVideoMimeTypeException($video->getMimeType());
        }

        $this->messageBus->dispatch(new CreateStreamCommand(
            uuid: Uuid::v4(),
            userId: $user->getId(),
            file: $video,
        ));
        
        return new JsonResponse(['message' => 'Video is being uploaded']);
    }

    #[Route('/video/url', name: 'video_url', methods: ['POST'])]
    public function uploadVideoByUrl(#[MapRequestPayload] UploadVideoByUrl $dto, #[CurrentUser] User $user): JsonResponse
    {
        $this->messageBus->dispatch(new CreateStreamByUrlCommand(
            uuid: Uuid::v4(),
            userId: $user->getId(),
            url: $dto->url,
        ));

        return new JsonResponse(['message' => 'Video is being downloaded']);
    }
}
