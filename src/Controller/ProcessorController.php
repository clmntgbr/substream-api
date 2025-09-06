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
use Symfony\Component\HttpFoundation\Response;
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
    public function getVideo(#[MapRequestPayload] GetVideoResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsUploaded($response->fileName, $response->originalName, $response->mimeType, $response->size);
        $this->streamRepository->save($stream);

        $this->messageBus->dispatch(new GetVideoSuccessCommand(
            streamId: $stream->getId(),
        ));

        return new Response();
    }

    #[Route('/get-video-url-failure', name: 'get_video_url_failure', methods: ['POST'])]
    public function getVideoFailure(#[MapRequestPayload] GetVideoFailureResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
        $this->streamRepository->save($stream);

        return new Response();
    }

    /**
     * @throws StreamNotFoundException
     */
    #[Route('/extract-sound', name: 'extract_sound', methods: ['POST'])]
    public function extractSound(#[MapRequestPayload] ExtractSoundResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsExtractedSound($response->audioFiles);
        $this->streamRepository->save($stream);

        $this->messageBus->dispatch(new ExtractSoundSuccessCommand(
            streamId: $stream->getId(),
        ));

        return new Response();
    }

    #[Route('/extract-sound-failure', name: 'extract_sound_failure', methods: ['POST'])]
    public function extractSoundFailure(#[MapRequestPayload] ExtractSoundFailureResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::EXTRACTED_SOUND_FAILED);
        $this->streamRepository->save($stream);

        return new Response();
    }

    /**
     * @throws StreamNotFoundException
     */
    #[Route('/generate-subtitles', name: 'generate_subtitles', methods: ['POST'])]
    public function generateSubtitles(#[MapRequestPayload] GenerateSubtitlesResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsGeneratedSubtitles($response->subtitle, $response->subtitleFiles);
        $this->streamRepository->save($stream);

        // $this->messageBus->dispatch(new GenerateSubtitlesSuccessCommand(
        //     streamId: $stream->getId(),
        // ));

        return new Response();
    }

    #[Route('/generate-subtitles-failure', name: 'generate_subtitles_failure', methods: ['POST'])]
    public function generateSubtitlesFailure(#[MapRequestPayload] GenerateSubtitlesFailureResponse $response): Response
    {
        $stream = $this->streamRepository->findOneBy(['id' => $response->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsFailed(StreamStatusEnum::GENERATED_SUBTITLES_FAILED);
        $this->streamRepository->save($stream);

        return new Response();
    }
}
