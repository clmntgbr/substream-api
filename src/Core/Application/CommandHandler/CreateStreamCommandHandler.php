<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Mapper\Stream\StreamMapper;
use App\Entity\Stream;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private StreamMapper $mapper,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): void
    {
        $entity = Stream::create(
            id: $command->getStreamId()?->value(),
            fileName: $command->getStreamFileName()?->value(),
            originalFileName: $command->getStreamOriginalFileName()?->value(),
            url: $command->getStreamUrl()?->value(),
        );

        $this->streamRepository->save($entity, true);
    }
}
