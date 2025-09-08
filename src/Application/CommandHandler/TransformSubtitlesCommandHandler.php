<?php

namespace App\Application\CommandHandler;

use App\Application\Command\TransformSubtitlesCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\TransformSubtitlesServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TransformSubtitlesCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private TransformSubtitlesServiceInterface $transformSubtitlesService,
    ) {
    }

    public function __invoke(TransformSubtitlesCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsTransformingSubtitlesProcessing();
        $this->transformSubtitlesService->transformSubtitles($stream);
    }
}
