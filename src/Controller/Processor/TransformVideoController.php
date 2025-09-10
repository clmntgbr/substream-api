<?php

namespace App\Controller\Processor;

use App\Application\Command\TransformSubtitleSuccessCommand;
use App\Application\Command\TransformVideoSuccessCommand;
use App\Dto\Processor\TransformSubtitleFailureResponse;
use App\Dto\Processor\TransformSubtitleResponse;
use App\Dto\Processor\TransformVideoFailureResponse;
use App\Dto\Processor\TransformVideoResponse;
use App\Enum\StreamStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/processor', name: 'processor_')]
class TransformVideoController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws StreamNotFoundException
     */
    #[Route('/transform-video', name: 'transform_video', methods: ['POST'])]
    public function transformVideo(#[MapRequestPayload] TransformVideoResponse $response): JsonResponse
    {
        $this->messageBus->dispatch(new TransformVideoSuccessCommand(
            videoFileTransformed: $response->videoFileTransformed,
            streamId: $response->streamId,
        ));

        return new JsonResponse();
    }

    #[Route('/transform-video-failure', name: 'transform_video_failure', methods: ['POST'])]
    public function transformVideoFailure(#[MapRequestPayload] TransformVideoFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_VIDEO_FAILED);
        $this->streamRepository->save($stream);
    }
}
