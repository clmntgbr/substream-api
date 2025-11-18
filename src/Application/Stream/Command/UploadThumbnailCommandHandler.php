<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Trait\WorkflowTrait;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use function Safe\fclose;
use function Safe\fopen;

#[AsMessageHandler]
class UploadThumbnailCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private readonly FilesystemOperator $localStorage,
        private readonly string $backendUrl,
    ) {
    }

    public function __invoke(UploadThumbnailCommand $command): void
    {
        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            return;
        }

        if (null !== $command->getThumbnail()) {
            $this->uploadThumbnail($command->getThumbnail(), $stream);

            return;
        }
    }

    private function uploadThumbnail(File $file, Stream $stream): void
    {
        try {
            $fileName = 'thumbnail.'.$file->guessExtension();
            $path = $stream->getId().'/'.$fileName;

            $handle = fopen($file->getPathname(), 'r');

            $this->localStorage->writeStream($path, $handle);

            if (is_resource($handle)) {
                fclose($handle);
            }

            $stream->setThumbnailUrl($this->backendUrl.'/uploads/'.$path);
        } catch (\Exception $e) {
            return;
        }
    }
}
