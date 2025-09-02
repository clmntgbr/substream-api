<?php

namespace App\Controller;

use App\Application\Command\CreateStreamByUrlCommand;
use App\Application\Command\CreateStreamCommand;
use App\Application\Command\UploadVideoByUrlCommand;
use App\Application\Command\UploadVideoCommand;
use App\Dto\Processor\GetVideoFailureResponse;
use App\Dto\Processor\GetVideoResponse;
use App\Dto\UploadVideoByUrl;
use App\Entity\User;
use App\Exception\InvalidVideoMimeTypeException;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/processor', name: 'processor_')]
class ProcessorController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/get-video', name: 'get_video', methods: ['POST'])]
    public function getVideo(#[MapRequestPayload] GetVideoResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsUploaded($response->fileName, $response->originalName, $response->mimeType, $response->size);
        $this->streamRepository->save($stream);
        return new Response();
    }

    #[Route('/get-video-failure', name: 'get_video_failure', methods: ['POST'])]
    public function getVideoFailure(#[MapRequestPayload] GetVideoFailureResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed();
        $this->streamRepository->save($stream);
        return new Response();
    }
}