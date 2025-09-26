<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamVideoCommand;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class CreateStreamVideoCommandHandler
{
    public function __construct(
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
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

        $violations = $this->validator->validate($command->videoFile, $constraints);

        if (count($violations) > 0) {
            throw new \RuntimeException($command->videoFile->getMimeType());
        }

        $uploadFileModel = $this->uploadFileService->uploadVideo($command->videoFile);

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->user,
            streamId: $uploadFileModel->id,
            fileName: $uploadFileModel->fileName,
            originalFileName: $uploadFileModel->originalFileName,
        ));

        return $createStreamModel;
    }
}
