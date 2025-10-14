<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamVideoCommand;
use App\Core\Application\Command\ExtractSoundCommand;
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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private S3ServiceInterface $s3Service,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private OptionRepository $optionRepository,
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

        $uploadFileModel = $this->s3Service->upload(
            uuid: $command->getStreamId(),
            file: $command->getFile(),
        );

        $option = $this->optionRepository->findByUuid($command->getOptionId());

        if (null === $option) {
            throw new OptionNotFoundException();
        }

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            optionId: $option->getId(),
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
