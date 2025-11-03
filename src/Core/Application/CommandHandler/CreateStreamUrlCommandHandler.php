<?php

declare(strict_types=1);

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
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            streamId: $command->getStreamId(),
            optionId: $command->getOptionId(),
            user: $command->getUser(),
            fileName: $command->getName(),
            originalFileName: $command->getName(),
            url: $command->getUrl(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException($command->getStreamId()->toRfc4122());
        }

        $thumbnailFile = $this->convertBase64ToFile($command->getThumbnailFile());

        $this->commandBus->dispatch(new UploadThumbnailCommand(
            streamId: $stream->getId(),
            thumbnailUrl: null,
            thumbnail: $thumbnailFile,
        ));

        $this->apply($stream, WorkflowTransitionEnum::UPLOADING);
        $this->streamRepository->saveAndFlush($stream);

        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
        ));

        return $createStreamModel;
    }

    private function convertBase64ToFile(string $base64Data): UploadedFile
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,(.+)$/', $base64Data, $matches)) {
            throw new \InvalidArgumentException('Invalid base64 image format');
        }

        $mimeType = 'image/'.$matches[1];
        $base64Content = $matches[2];

        $imageData = base64_decode($base64Content);
        if (false === $imageData) {
            throw new \InvalidArgumentException('Invalid base64 data');
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'thumbnail_');
        if (false === $tempFile) {
            throw new \RuntimeException('Could not create temporary file');
        }

        if (false === file_put_contents($tempFile, $imageData)) {
            throw new \RuntimeException('Could not write to temporary file');
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        return new UploadedFile(
            path: $tempFile,
            originalName: 'thumbnail.'.$extension,
            mimeType: $mimeType,
            error: \UPLOAD_ERR_OK,
            test: true
        );
    }
}
