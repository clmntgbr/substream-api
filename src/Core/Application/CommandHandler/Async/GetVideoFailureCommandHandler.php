<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Core\Application\Command\Async\GetVideoFailureCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoFailureCommandHandler
{

    public function __construct(
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(GetVideoFailureCommand $command): void
    {
        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsUploadFailed();
        $this->streamRepository->save($stream, true);
    }
}
