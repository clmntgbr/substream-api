<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Command\Async\GetVideoSuccessCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoSuccessCommandHandler
{

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(GetVideoSuccessCommand $command): void
    {
        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $stream->setFileName($command->getFileName());
            $stream->setOriginalFileName($command->getOriginalFileName());
            $stream->setMimeType($command->getMimeType());
            $stream->setSize($command->getSize());
            $stream->markAsUploaded();

            $this->commandBus->dispatch(new ExtractSoundCommand(
                streamId: $stream->getId(),
                fileName: $command->getFileName(),
            ));
        } catch (\Throwable $exception) {
            $stream->markAsUploadFailed();
        } finally {
            $this->streamRepository->save($stream, true);
        }
    }
}
