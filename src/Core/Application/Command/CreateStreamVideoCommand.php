<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateStreamVideoCommand implements SyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public UploadedFile $videoFile,
        public User $user,
    ) {
    }

    public function getData(): array
    {
        return [];
    }

    public function supports(): bool
    {
        return false;
    }
}
