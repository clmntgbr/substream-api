<?php

namespace App\Core\Application\CommandHandler\Sync;

use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Command\Sync\CreateStreamCommand;
use App\Core\Application\Command\Sync\CreateStreamVideoCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Enum\StreamStatusEnum;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function __invoke(CreateStreamVideoCommand $command): CreateStreamModel
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($command->getFile(), $constraints);

        if (count($violations) > 0) {
            throw new \RuntimeException($command->getFile()->getMimeType());
        }

        $uploadFileModel = $this->uploadFileService->uploadVideo(
            streamId: $command->getStreamId(),
            file: $command->getFile(),
        );

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            fileName: $uploadFileModel->getFileName(),
            originalFileName: $uploadFileModel->getOriginalFileName(),
            mimeType: $command->getFile()->getMimeType(),
            size: $command->getFile()->getSize(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }
        
        $this->apply($stream, WorkflowTransitionEnum::UPLOADED_SIMPLE);
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new ExtractSoundCommand(
            streamId: $command->getStreamId(),
            fileName: $uploadFileModel->getFileName(),
        ));

        return $createStreamModel;
    }
}
