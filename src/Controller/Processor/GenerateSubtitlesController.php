<?php

namespace App\Controller\Processor;

use App\Application\Command\GenerateSubtitlesSuccessCommand;
use App\Dto\Processor\GenerateSubtitlesFailureResponse;
use App\Dto\Processor\GenerateSubtitlesResponse;
use App\Enum\StreamStatusEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/processor', name: 'processor_')]
class GenerateSubtitlesController extends AbstractController
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
    #[Route('/generate-subtitles', name: 'generate_subtitles', methods: ['POST'])]
    public function generateSubtitles(#[MapRequestPayload] GenerateSubtitlesResponse $response): void
    {
        $this->messageBus->dispatch(new GenerateSubtitlesSuccessCommand(
            subtitleSrtFiles: $response->subtitleSrtFiles,
            subtitleSrtFile: $response->subtitleSrtFile,
            streamId: $response->streamId,
        ));
    }

    #[Route('/generate-subtitles-failure', name: 'generate_subtitles_failure', methods: ['POST'])]
    public function generateSubtitlesFailure(#[MapRequestPayload] GenerateSubtitlesFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::GENERATED_SUBTITLES_FAILED);
        $this->streamRepository->save($stream);
    }
}
