<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\UploadVideoCommand;
use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Service\UploadServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class UploadVideoCommandHandler
{
    public function __construct(
        private UploadServiceInterface $uploadService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(UploadVideoCommand $command): StreamId
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($command->file, $constraints);

        if (count($violations) > 0) {
            throw new \RuntimeException($command->file->getMimeType());
        }

        $uploadVideo = $this->uploadService->uploadVideo($command->file);

        $this->commandBus->dispatch(new CreateStreamCommand(
            id: $uploadVideo->id,
            fileName: StreamFileName::create($uploadVideo->fileName->value()),
            originalFileName: StreamOriginalFileName::create($uploadVideo->originalFileName->value()),
        ));

        return $uploadVideo->id;
    }
}
