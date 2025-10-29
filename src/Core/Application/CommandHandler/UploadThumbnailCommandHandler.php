<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\UploadThumbnailCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Stream;
use App\Repository\StreamRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

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

        if (null !== $command->getThumbnailUrl()) {
            $this->uploadThumbnailFromUrl($command->getThumbnailUrl(), $stream);

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

    private function uploadThumbnailFromUrl(string $url, Stream $stream): void
    {
        try {
            $imageContents = @file_get_contents($url);
            if (false === $imageContents) {
                return;
            }

            $imageInfo = @getimagesizefromstring($imageContents);
            $mime = $imageInfo['mime'] ?? null;
            $extension = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                default => 'jpg',
            };

            $fileName = 'thumbnail.'.$extension;
            $path = $stream->getId().'/'.$fileName;

            $tempStream = fopen('php://temp', 'r+');
            fwrite($tempStream, $imageContents);
            rewind($tempStream);

            $this->localStorage->writeStream($path, $tempStream);

            fclose($tempStream);

            $stream->setThumbnailUrl($this->backendUrl.'/uploads/'.$path);
        } catch (\Throwable $e) {
            return;
        }
    }
}
