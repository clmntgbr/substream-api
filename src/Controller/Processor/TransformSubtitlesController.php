<?php

namespace App\Controller\Processor;

use App\Application\Command\TransformSubtitlesSuccessCommand;
use App\Dto\Processor\TransformSubtitlesFailureResponse;
use App\Dto\Processor\TransformSubtitlesResponse;
use App\Enum\StreamStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/processor', name: 'processor_')]
class TransformSubtitlesController extends AbstractController
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
    #[Route('/transform-subtitles', name: 'transform_subtitles', methods: ['POST'])]
    public function transformSubtitles(#[MapRequestPayload] TransformSubtitlesResponse $response): void
    {
        $this->messageBus->dispatch(new TransformSubtitlesSuccessCommand(
            subtitleAssFile: $response->subtitleAssFile,
            streamId: $response->streamId,
        ));
    }

    #[Route('/transform-subtitles-failure', name: 'transform_subtitles_failure', methods: ['POST'])]
    public function transformSubtitlesFailure(#[MapRequestPayload] TransformSubtitlesFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_SUBTITLES_FAILED);
        $this->streamRepository->save($stream);
    }
}
