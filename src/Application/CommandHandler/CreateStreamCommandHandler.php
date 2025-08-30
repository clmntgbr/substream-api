<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateStreamCommand;
use App\Entity\Stream;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): void
    {
        $stream = (new Stream())->create(
            uuid: $command->uuid,
            fileName: $command->fileName,
            mimeType: $command->mimeType,
            size: $command->size,
        );

        $this->streamRepository->save($stream, true);
    }
}
