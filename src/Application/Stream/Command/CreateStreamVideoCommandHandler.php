<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Core\Command\ExtractSoundCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Option\Repository\OptionRepository;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Infrastructure\Storage\S3\S3StorageServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private S3StorageServiceInterface $s3StorageService,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private OptionRepository $optionRepository,
    ) {
    }

    public function __invoke(CreateStreamVideoCommand $command): Stream
    {
        $uploadedFileDto = $this->s3StorageService->upload(
            uuid: $command->getStreamId(),
            file: $command->getFile(),
        );

        $option = $this->optionRepository->findByUuid($command->getOptionId());

        if (null === $option) {
            throw new \Exception($command->getOptionId()->toRfc4122());
        }

        $stream = $this->commandBus->dispatch(new CreateStreamCommand(
            streamId: $command->getStreamId(),
            optionId: $option->getId(),
            user: $command->getUser(),
            fileName: $uploadedFileDto->fileName,
            originalFileName: $uploadedFileDto->originalFileName,
            duration: (int) $command->getDuration(),
            mimeType: $command->getFile()->getMimeType(),
            size: $command->getFile()->getSize(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new \Exception($command->getStreamId()->toRfc4122());
        }

        $this->commandBus->dispatch(new UploadThumbnailCommand(
            streamId: $stream->getId(),
            thumbnailUrl: null,
            thumbnail: $command->getThumbnail(),
        ));

        $this->apply($stream, WorkflowTransitionEnum::UPLOADED_SIMPLE);
        $this->streamRepository->saveAndFlush($stream);

        $this->commandBus->dispatch(new ExtractSoundCommand(
            streamId: $command->getStreamId(),
            fileName: $uploadedFileDto->fileName,
        ));

        return $stream;
    }
}
