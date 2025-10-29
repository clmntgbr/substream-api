<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Command\UploadThumbnailCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            originalFileName: $command->getName(),
            fileName: $command->getName(),
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
            optionId: $command->getOptionId(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $thumbnailFile = $this->convertBase64ToFile($command->getThumbnailFile());

        $this->commandBus->dispatch(new UploadThumbnailCommand(
            streamId: $stream->getId(),
            thumbnailUrl: null,
            thumbnail: $thumbnailFile,
        ));

        $this->apply($stream, WorkflowTransitionEnum::UPLOADING);
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
        ));

        sleep(3);

        return $createStreamModel;
    }

    private function convertBase64ToFile(string $base64Data): UploadedFile
    {
        // Parse the data URL to extract the mime type and base64 content
        if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,(.+)$/', $base64Data, $matches)) {
            throw new \InvalidArgumentException('Invalid base64 image format');
        }

        $mimeType = 'image/'.$matches[1];
        $base64Content = $matches[2];

        // Decode the base64 content
        $imageData = base64_decode($base64Content);
        if (false === $imageData) {
            throw new \InvalidArgumentException('Invalid base64 data');
        }

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'thumbnail_');
        if (false === $tempFile) {
            throw new \RuntimeException('Could not create temporary file');
        }

        // Write the image data to the temporary file
        if (false === file_put_contents($tempFile, $imageData)) {
            throw new \RuntimeException('Could not write to temporary file');
        }

        // Determine the file extension based on mime type
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        // Create an UploadedFile instance
        return new UploadedFile(
            path: $tempFile,
            originalName: 'thumbnail.'.$extension,
            mimeType: $mimeType,
            error: \UPLOAD_ERR_OK,
            test: true
        );
    }
}
