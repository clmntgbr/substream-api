<?php

namespace App\Controller\Processor;

use App\Application\Command\GetVideoSuccessCommand;
use App\Dto\Processor\GetVideoFailureResponse;
use App\Dto\Processor\GetVideoResponse;
use App\Enum\StreamStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/processor', name: 'processor_')]
class GetVideoUrlController extends AbstractController
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
    #[Route('/get-video-url', name: 'get_video_url', methods: ['POST'])]
    public function getVideoUrl(#[MapRequestPayload] GetVideoResponse $response): void
    {
        $this->messageBus->dispatch(new GetVideoSuccessCommand(
            videoFileName: $response->videoFileName,
            originalFileName: $response->originalFileName,
            mimeType: $response->mimeType,
            size: $response->size,
            streamId: $response->streamId,
        ));
    }

    #[Route('/get-video-url-failure', name: 'get_video_url_failure', methods: ['POST'])]
    public function getVideoUrlFailure(#[MapRequestPayload] GetVideoFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
        $this->streamRepository->save($stream);
    }
}
