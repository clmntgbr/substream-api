<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\ExtractSoundSuccessCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ExtractSoundSuccessCommandHandler
{

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(ExtractSoundSuccessCommand $command): void
    {
        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $stream->setAudioFiles($command->getAudioFiles());
            $stream->markAsExtractSoundCompleted();
        } catch (\Throwable $exception) {
            $stream->markAsExtractSoundFailed();
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
