<?php

namespace App\Controller\Processor;

use App\Core\Application\Command\GetVideoFailureCommand;
use App\Core\Application\Command\GetVideoSuccessCommand;
use App\Dto\Processor\GetVideoProcessorFailure;
use App\Dto\Processor\GetVideoProcessorSuccess;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class GetVideoProcessorController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/processor/get-video-processor-success', name: 'api_processor_get_video_processor_success', methods: ['POST'])]
    public function getVideoProcessorSuccess(#[MapRequestPayload] GetVideoProcessorSuccess $response): JsonResponse
    {
        $this->commandBus->dispatch(new GetVideoSuccessCommand(
            streamId: $response->getStreamId(),
            fileName: $response->getFileName(),
            originalFileName: $response->getOriginalFileName(),
            mimeType: $response->getMimeType(),
            size: $response->getSize(),
        ));

        return Response::successResponse([
            'stream_id' => $response->getStreamId(),
        ]);
    }

    #[Route('/processor/get-video-processor-failure', name: 'api_processor_get_video_processor_failure', methods: ['POST'])]
    public function getVideoProcessorFailure(#[MapRequestPayload] GetVideoProcessorFailure $response): JsonResponse
    {
        $this->commandBus->dispatch(new GetVideoFailureCommand(
            streamId: $response->getStreamId(),
        ));

        return Response::successResponse([
            'stream_id' => $response->getStreamId(),
        ]);
    }
}
