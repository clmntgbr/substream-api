<?php

namespace App\Application\CommandHandler;

use App\Application\Command\ExtractSoundCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\ExtractSoundServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ExtractSoundCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private ExtractSoundServiceInterface $extractSoundService,
    ) {
    }

    public function __invoke(ExtractSoundCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsExtractingSoundProcessing();
        $this->extractSoundService->extractSound($stream);
    }
}
