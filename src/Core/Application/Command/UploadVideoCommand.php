<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadVideoCommand implements SyncCommandInterface
{
    public function __construct(
        public UploadedFile $file,
    ) {
    }
}
