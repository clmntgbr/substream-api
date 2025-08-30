<?php

namespace App\Service;

use App\Application\Command\CreateStreamCommand;
use App\Entity\User;
use App\Exception\InvalidVideoMimeTypeException;
use App\Exception\UnauthorizedHttpException;
use App\Exception\UploadVideoException;
use League\Flysystem\FilesystemOperator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadVideoService implements UploadVideoServiceInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private FilesystemOperator $awsStorage,
        private Security $security,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function upload(UploadedFile $file): void
    {
        $constraints = new File([
            'mimeTypes' => [
                'video/mp4',
            ],
            'mimeTypesMessage' => 'Please upload a valid video file (MP4).',
        ]);

        $violations = $this->validator->validate($file, $constraints);

        if (count($violations) > 0) {
            throw new InvalidVideoMimeTypeException($file->getMimeType());
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException();
        }

        try {
            $uuid = Uuid::v4();
            $fileName = Uuid::v4()->toString().'.'.$file->guessExtension();
            $path = $uuid.'/'.$fileName;

            $stream = fopen($file->getPathname(), 'r');

            $this->awsStorage->writeStream($path, $stream, [
                'visibility' => 'public',
                'mimetype' => $file->getMimeType(),
            ]);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $this->messageBus->dispatch(new CreateStreamCommand(
                uuid: $uuid,
                fileName: $fileName,
                originalName: $file->getClientOriginalName(),
                mimeType: $file->getMimeType(),
                size: $file->getSize(),
                userId: $user->getId(),
            ), [new AmqpStamp('async-high')]);
        } catch (\Exception $_) {
            throw new UploadVideoException('Unable to write file');
        }
    }

    public function uploadByUrl(string $url): void
    {
        throw new \Exception('Not implemented');
    }
}
