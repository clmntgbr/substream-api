<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Domain\Option\Repository\OptionRepository;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Exception\OptionNotFoundException;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private EventDispatcherInterface $eventDispatcher,
        private OptionRepository $optionRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): Stream
    {
        $option = $this->optionRepository->findByUuid($command->getOptionId());

        if (null === $option) {
            throw new OptionNotFoundException($command->getOptionId()->toRfc4122());
        }

        $stream = Stream::create(
            id: $command->getStreamId(),
            user: $command->getUser(),
            option: $option,
            fileName: $command->getFileName(),
            originalFileName: $command->getOriginalFileName(),
            url: $command->getUrl(),
            mimeType: $command->getMimeType(),
            size: $command->getSize(),
            duration: $command->getDuration(),
        );

        $this->streamRepository->saveAndFlush($stream);

        $this->commandBus->dispatch(new DeleteStreamAfter14DaysCommand(
            streamId: $stream->getId(),
        ));

        return $stream;
    }
}
