<?php

namespace App\Controller\Processor;

use App\Application\Command\TransformSubtitleSuccessCommand;
use App\Dto\Processor\TransformSubtitleFailureResponse;
use App\Dto\Processor\TransformSubtitleResponse;
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
class TransformSubtitleController extends AbstractController
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
    #[Route('/transform-subtitle', name: 'transform_subtitle', methods: ['POST'])]
    public function transformSubtitle(#[MapRequestPayload] TransformSubtitleResponse $response): JsonResponse
    {
        $this->messageBus->dispatch(new TransformSubtitleSuccessCommand(
            subtitleAssFile: $response->subtitleAssFile,
            streamId: $response->streamId,
        ));

        return new JsonResponse();
    }

    #[Route('/transform-subtitle-failure', name: 'transform_subtitle_failure', methods: ['POST'])]
    public function transformSubtitleFailure(#[MapRequestPayload] TransformSubtitleFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_SUBTITLE_FAILED);
        $this->streamRepository->save($stream);
    }
}
