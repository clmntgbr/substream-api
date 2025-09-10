<?php

namespace App\Application\CommandHandler;

use App\Application\Command\TransformSubtitleCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\TransformSubtitleServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TransformSubtitleCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private TransformSubtitleServiceInterface $transformSubtitleService,
    ) {
    }

    public function __invoke(TransformSubtitleCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsTransformingSubtitlesProcessing();
        $this->transformSubtitleService->transformSubtitle($stream);
    }
}
