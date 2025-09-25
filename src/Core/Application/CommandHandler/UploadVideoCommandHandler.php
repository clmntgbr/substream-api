<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\UploadVideoCommand;
use App\Core\Domain\Aggregate\StreamModel;
use App\Core\Domain\Aggregate\UploadVideoModel;
use App\Core\Application\Mapper\Stream\StreamMapper;
use App\Entity\Stream;
use App\Service\UploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class UploadVideoCommandHandler
{
    public function __construct(
        private UploadServiceInterface $uploadService,
        private ValidatorInterface $validator,
    ) {
    }

    public function __invoke(UploadVideoCommand $command): UploadVideoModel
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($command->file, $constraints);

        if (count($violations) > 0) {
            throw new \RuntimeException($command->file->getMimeType());
        }
        
        return $this->uploadService->uploadVideo($command->file);
    }
}
