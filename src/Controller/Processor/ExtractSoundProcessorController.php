<?php

namespace App\Controller\Processor;

use App\Core\Application\Command\ExtractSoundProcessorFailureCommand;
use App\Core\Application\Command\ExtractSoundProcessorSuccessCommand;
use App\Core\Application\Command\GetVideoProcessorFailureCommand;
use App\Core\Application\Command\GetVideoProcessorSuccessCommand;
use App\Dto\Processor\ExtractSoundProcessorFailure;
use App\Dto\Processor\ExtractSoundProcessorSuccess;
use App\Dto\Processor\GetVideoProcessorFailure;
use App\Dto\Processor\GetVideoProcessorSuccess;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class ExtractSoundProcessorController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    #[Route('/processor/extract-sound-processor-success', name: 'api_processor_extract_sound_processor_success', methods: ['POST'])]
    public function extractSoundProcessorSuccess(#[MapRequestPayload] ExtractSoundProcessorSuccess $response): JsonResponse
    {
        $this->commandBus->dispatch(new ExtractSoundProcessorSuccessCommand(
            streamId: $response->getStreamId(),
            audioFiles: $response->getAudioFiles(),
        ));

        return Response::successResponse([
            'stream_id' => $response->getStreamId(),
        ]);
    }

    #[Route('/processor/extract-sound-processor-failure', name: 'api_processor_extract_sound_processor_failure', methods: ['POST'])]
    public function extractSoundProcessorFailure(#[MapRequestPayload] ExtractSoundProcessorFailure $response): JsonResponse
    {
        $this->commandBus->dispatch(new ExtractSoundProcessorFailureCommand(
            streamId: $response->getStreamId(),
        ));

        return Response::successResponse([
            'stream_id' => $response->getStreamId(),
        ]);
    }
}
