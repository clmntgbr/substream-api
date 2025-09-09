<?php

namespace App\Application\CommandHandler;

use App\Application\Command\GenerateSubtitlesCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\GenerateSubtitlesServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GenerateSubtitlesCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private GenerateSubtitlesServiceInterface $generateSubtitlesService,
    ) {
    }

    public function __invoke(GenerateSubtitlesCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsGeneratingSubtitlesProcessing();
        $this->generateSubtitlesService->generateSubtitles($stream);
    }
}
