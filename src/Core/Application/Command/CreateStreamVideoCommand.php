<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateStreamVideoCommand implements SyncCommandInterface
{
    public function __construct(
        public UploadedFile $videoFile,
        public User $user,
    ) {
    }
}
