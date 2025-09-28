<?php

namespace App\Controller\Processor;

use App\Core\Application\Command\GetVideoProcessorFailureCommand;
use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
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
    public function getVideoProcessorSuccess(#[MapRequestPayload] GetVideoProcessorSuccess $getVideoProcessorSuccess): JsonResponse
    {
        $this->commandBus->dispatch(new GetVideoProcessorSuccessCommand(
            streamId: $getVideoProcessorSuccess->getStreamId(),
            jobId: $getVideoProcessorSuccess->getJobId(),
            fileName: $getVideoProcessorSuccess->getFileName(),
            originalFileName: $getVideoProcessorSuccess->getOriginalFileName(),
            mimeType: $getVideoProcessorSuccess->getMimeType(),
            size: $getVideoProcessorSuccess->getSize(),
        ));

        return Response::successResponse([
            'stream_id' => $getVideoProcessorSuccess->getStreamId(),
            'job_id' => $getVideoProcessorSuccess->getJobId(),
        ]);
    }

    #[Route('/processor/get-video-processor-failure', name: 'api_processor_get_video_processor_failure', methods: ['POST'])]
    public function getVideoProcessorFailure(#[MapRequestPayload] GetVideoProcessorFailure $getVideoProcessorFailure): JsonResponse
    {
        $response = $this->commandBus->dispatch(new GetVideoProcessorFailureCommand(
            streamId: $getVideoProcessorFailure->getStreamId(),
            jobId: $getVideoProcessorFailure->getJobId(),
            errorMessage: $getVideoProcessorFailure->getErrorMessage(),
        ));

        dump($response);
        die;

        return Response::successResponse([
            'stream_id' => $getVideoProcessorFailure->getStreamId(),
            'job_id' => $getVideoProcessorFailure->getJobId(),
        ]);
    }
}
