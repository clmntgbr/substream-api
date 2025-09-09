<?php

namespace App\Controller;

use App\Application\Command\ExtractSoundSuccessCommand;
use App\Application\Command\GenerateSubtitlesSuccessCommand;
use App\Application\Command\GetVideoSuccessCommand;
use App\Dto\Processor\ExtractSoundFailureResponse;
use App\Dto\Processor\ExtractSoundResponse;
use App\Dto\Processor\GenerateSubtitlesFailureResponse;
use App\Dto\Processor\GenerateSubtitlesResponse;
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
class ProcessorController extends AbstractController
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
    public function getVideo(#[MapRequestPayload] GetVideoResponse $response): void
    {
        $this->messageBus->dispatch(new GetVideoSuccessCommand(
            fileName: $response->fileName,
            originalName: $response->originalName,
            mimeType: $response->mimeType,
            size: $response->size,
            streamId: $response->streamId,
        ));
    }

    #[Route('/get-video-url-failure', name: 'get_video_url_failure', methods: ['POST'])]
    public function getVideoFailure(#[MapRequestPayload] GetVideoFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
        $this->streamRepository->save($stream);
    }

    /**
     * @throws StreamNotFoundException
     */
    #[Route('/extract-sound', name: 'extract_sound', methods: ['POST'])]
    public function extractSound(#[MapRequestPayload] ExtractSoundResponse $response): void
    {
        $this->messageBus->dispatch(new ExtractSoundSuccessCommand(
            streamId: $response->streamId,
            audioFiles: $response->audioFiles,
        ));
    }

    #[Route('/extract-sound-failure', name: 'extract_sound_failure', methods: ['POST'])]
    public function extractSoundFailure(#[MapRequestPayload] ExtractSoundFailureResponse $response): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::EXTRACTED_SOUND_FAILED);
        $this->streamRepository->save($stream);
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
