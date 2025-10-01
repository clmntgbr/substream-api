<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundProcessorFailureCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExtractSoundProcessorFailureCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(ExtractSoundProcessorFailureCommand $command): void
    {
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsExtractSoundFailed();
        $this->streamRepository->save($stream, true);
    }
}
