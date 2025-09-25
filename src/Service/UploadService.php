<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Application\Mapper\UploadVideo\UploadVideoMapperInterface;
use App\Core\Domain\Aggregate\UploadVideoModel;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadService implements UploadServiceInterface
{
    public function __construct(
        private FilesystemOperator $awsStorage,
        private UploadVideoMapperInterface $uploadVideoMapper,
    ) {
    }

    public function uploadVideo(UploadedFile $file): UploadVideoModel
    {
        $uuid = Uuid::v4();
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

        return $this->uploadVideoMapper->create($fileName, $file->getClientOriginalName(), (string) $uuid);
    }
}
