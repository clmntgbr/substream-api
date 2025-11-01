<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;
use App\Event\CreateStreamEvent;
use App\Exception\OptionNotFoundException;
use App\Repository\OptionRepository;
use App\Repository\StreamRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CreateStreamMapperInterface $createStreamMapper,
        private CommandBusInterface $commandBus,
        private EventDispatcherInterface $eventDispatcher,
        private OptionRepository $optionRepository,
        private PublishServiceInterface $publishService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): CreateStreamModel
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

        $this->eventDispatcher->dispatch(new CreateStreamEvent($command->getStreamId()));

        return $this->createStreamMapper->fromEntity($stream);
    }
}
