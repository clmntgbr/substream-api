<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamVideoCommand;
use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Trait\JobTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    use JobTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private JobRepository $jobRepository,
    ) {
        $this->jobRepository = $jobRepository;
    }

    public function __invoke(CreateStreamVideoCommand $command): CreateStreamModel
    {
        $this->findByJobId($command->getJobId());
        
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($command->getVideoFile(), $constraints);

        if (count($violations) > 0) {
            throw new \RuntimeException($command->getVideoFile()->getMimeType());
        }

        $uploadFileModel = $this->uploadFileService->uploadVideo($command->getStreamId(), $command->getVideoFile());

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            fileName: $uploadFileModel->fileName,
            originalFileName: $uploadFileModel->originalFileName,
            mimeType: $command->getVideoFile()->getMimeType(),
            size: $command->getVideoFile()->getSize(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsUploaded();
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new ExtractSoundCommand(
            streamId: $command->getStreamId(),
        ));

        $this->markJobAsSuccess();
        return $createStreamModel;
    }
}
