<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\ExtractSoundProcessorSuccessCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExtractSoundProcessorSuccessCommandHandler
{

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(ExtractSoundProcessorSuccessCommand $command): void
    {
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $stream->setAudioFiles($command->audioFiles);
            $stream->markAsExtractSoundCompleted();
        } catch (\Throwable $exception) {
            $stream->markAsExtractSoundFailed();
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
