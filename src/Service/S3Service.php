<?php

namespace App\Service;

use App\Core\Application\Mapper\UploadFileMapperInterface;
use App\Core\Domain\Aggregate\UploadFileModel;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class S3Service implements S3ServiceInterface
{
    public function __construct(
        private FilesystemOperator $awsStorage,
        private UploadFileMapperInterface $uploadFileMapper,
    ) {
    }

    public function deleteAll(Uuid $uuid): void
    {
        $streamPath = $uuid->toRfc4122();

        $files = $this->awsStorage->listContents($streamPath, true);

        foreach ($files as $file) {
            if ('file' === $file['type']) {
                $this->awsStorage->delete($file['path']);
            }
        }
    }

    public function delete(Uuid $uuid, string $fileName): void
    {
        $this->awsStorage->delete($uuid.'/'.$fileName);
    }

    public function download(Uuid $uuid, string $fileName): string
    {
        $tmpDir = sys_get_temp_dir() . '/'.$uuid->toRfc4122();
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $tmpFilePath = $tmpDir . '/' . basename($fileName);

        $stream = $this->awsStorage->readStream($uuid.'/'.$fileName);

        $tmpFile = fopen($tmpFilePath, 'w');
        stream_copy_to_stream($stream, $tmpFile);
        
        if (is_resource($stream)) {
            fclose($stream);
        }
        
        if (is_resource($tmpFile)) {
            fclose($tmpFile);
        }

        return $tmpFilePath;
    }

    public function upload(Uuid $uuid, UploadedFile $file): UploadFileModel
    {
        $fileName = $uuid.'.'.$file->guessExtension();
        $path = $uuid.'/'.$fileName;

        $handle = fopen($file->getPathname(), 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
            'mimetype' => $file->getMimeType(),
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }

        return $this->uploadFileMapper->create(
            fileName: $fileName,
            originalFileName: $file->getClientOriginalName(),
            id: $uuid,
        );
    }
}
