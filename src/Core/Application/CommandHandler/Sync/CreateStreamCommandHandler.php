<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Sync;

use App\Core\Application\Command\Sync\CreateStreamCommand;
use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Shared\Application\Bus\CommandBusInterface;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CreateStreamMapperInterface $createStreamMapper,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): CreateStreamModel
    {
        $stream = Stream::create(
            id: $command->getStreamId(),
            user: $command->getUser(),
            fileName: $command->getFileName(),
            originalFileName: $command->getOriginalFileName(),
            url: $command->getUrl(),
            mimeType: $command->getMimeType(),
            size: $command->getSize(),
        );

        $this->streamRepository->save($stream, true);

        return $this->createStreamMapper->fromEntity($stream);
    }
}
