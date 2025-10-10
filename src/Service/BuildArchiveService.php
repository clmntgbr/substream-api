<?php

namespace App\Service;

use App\Entity\Stream;
use App\Util\Slugify;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BuildArchiveService implements BuildArchiveServiceInterface
{
    public function __construct(
        private S3ServiceInterface $s3Service,
        private string $uploadDirectory,
    ) {
    }

    public function build(Stream $stream): File
    {
        $archive = new \ZipArchive();

        $archivePath = sprintf(
            '%s%s.zip',
            $this->uploadDirectory,
            $stream->getId()->toRfc4122(),
        );

        if (true !== $archive->open($archivePath, \ZipArchive::CREATE)) {
            throw new UnprocessableEntityHttpException('Unable to create temporary zip archive');
        }

        foreach ($stream->getChunkFileNames() as $file) {
            $filePath = $this->s3Service->download($stream->getId(), $file);
            $archive->addFile($filePath, $file);
        }

        $archive->close();

        return new File($archivePath);
    }
}
