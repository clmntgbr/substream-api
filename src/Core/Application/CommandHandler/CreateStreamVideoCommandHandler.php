<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamVideoCommand;
use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\UploadThumbnailCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\OptionNotFoundException;
use App\Exception\StreamNotFoundException;
use App\Repository\OptionRepository;
use App\Repository\StreamRepository;
use App\Service\S3ServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private S3ServiceInterface $s3Service,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private OptionRepository $optionRepository,
    ) {
    }

    public function __invoke(CreateStreamVideoCommand $command): CreateStreamModel
    {
        $uploadFileModel = $this->s3Service->upload(
            uuid: $command->getStreamId(),
            file: $command->getFile(),
        );

        $option = $this->optionRepository->findByUuid($command->getOptionId());

        if (null === $option) {
            throw new OptionNotFoundException($command->getOptionId()->toRfc4122());
        }

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            streamId: $command->getStreamId(),
            optionId: $option->getId(),
            user: $command->getUser(),
            fileName: $uploadFileModel->getFileName(),
            originalFileName: $uploadFileModel->getOriginalFileName(),
            duration: (int) $command->getDuration(),
            mimeType: $command->getFile()->getMimeType(),
            size: $command->getFile()->getSize(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException($command->getStreamId()->toRfc4122());
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
            fileName: $uploadFileModel->getFileName(),
        ));

        return $createStreamModel;
    }
}
