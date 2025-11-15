<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Core\Command\GetVideoCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

use function Safe\base64_decode;
use function Safe\file_put_contents;
use function Safe\preg_match;
use function Safe\tempnam;

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

    public function __invoke(CreateStreamUrlCommand $command): Stream
    {
        $stream = $this->commandBus->dispatch(new CreateStreamCommand(
            streamId: $command->getStreamId(),
            optionId: $command->getOptionId(),
            user: $command->getUser(),
            fileName: $command->getName(),
            originalFileName: $command->getName(),
            url: $command->getUrl(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new \Exception($command->getStreamId()->toRfc4122());
        }

        $thumbnailFile = $this->convertBase64ToFile($command->getThumbnailFile());

        $this->commandBus->dispatch(new UploadThumbnailCommand(
            streamId: $stream->getId(),
            thumbnailUrl: null,
            thumbnail: $thumbnailFile,
        ));

        $this->apply($stream, WorkflowTransitionEnum::UPLOADING);
        $this->streamRepository->saveAndFlush($stream);

        dump('get video');
        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
        ));

        return $stream;
    }

    private function convertBase64ToFile(string $base64Data): UploadedFile
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,(.+)$/', $base64Data, $matches)) {
            throw new \Exception('Invalid thumbnail format');
        }

        $mimeType = 'image/'.$matches[1];
        $base64Content = $matches[2];

        $imageData = base64_decode($base64Content);
        $tempFile = tempnam(sys_get_temp_dir(), 'thumbnail_');
        file_put_contents($tempFile, $imageData);

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
