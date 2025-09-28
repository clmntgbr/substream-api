<?php

namespace App\Service;

use App\Core\Application\Mapper\UploadFileMapperInterface;
use App\Core\Domain\Aggregate\UploadFileModel;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class UploadFileService implements UploadFileServiceInterface
{
    public function __construct(
        private FilesystemOperator $awsStorage,
        private UploadFileMapperInterface $uploadFileMapper,
    ) {
    }

    public function uploadVideo(Uuid $streamId, UploadedFile $file): UploadFileModel
    {
        $fileName = $streamId.'.'.$file->guessExtension();
        $path = $streamId.'/'.$fileName;

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
            id: $streamId,
        );
    }
}
