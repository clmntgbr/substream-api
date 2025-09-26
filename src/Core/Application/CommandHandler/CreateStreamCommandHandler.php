<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CreateStreamMapperInterface $createStreamMapper,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): CreateStreamModel
    {
        $stream = Stream::create(
            id: $command->streamId,
            user: $command->user,
            fileName: $command->fileName,
            originalFileName: $command->originalFileName,
            url: $command->url,
        );

        $this->streamRepository->save($stream, true);

        return $this->createStreamMapper->fromEntity($stream);
    }
}
