<?php

namespace App\Service;

use App\Application\Command\CreateStreamCommand;
use App\Client\Processor\GetVideoByUrlProcessor;
use App\Client\Processor\GetVideoByUrlProcessorInterface;
use App\Dto\Processor\GetVideoByUrl;
use App\Entity\Stream;
use App\Entity\User;
use App\Exception\InvalidVideoMimeTypeException;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Exception\UnauthorizedHttpException;
use App\Exception\UploadVideoException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
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
        private UserRepository $userRepository,
        private StreamRepository $streamRepository,
        private MessageBusInterface $messageBus,
        private GetVideoByUrlProcessorInterface $getVideoByUrlProcessor,
    ) {
    }

    public function upload(UploadedFile $file, Uuid $userId, Uuid $streamId): void
    {
        /** @var ?User $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (null === $user) {
            throw new UnauthorizedHttpException();
        }

        /** @var ?Stream $stream */
        $stream = $this->streamRepository->findOneBy(['id' => $streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $fileName = Uuid::v4()->toString().'.'.$file->guessExtension();
            $path = $streamId.'/'.$fileName;

            $handle = fopen($file->getPathname(), 'r');

            $this->awsStorage->writeStream($path, $handle, [
                'visibility' => 'public',
                'mimetype' => $file->getMimeType(),
            ]);

            if (is_resource($handle)) {
                fclose($handle);
            }

            $stream->markAsUploaded($fileName, $file->getClientOriginalName(), $file->getMimeType(), $file->getSize());
        } catch (\Exception $_) {
            $stream->markAsFailed($file->getClientOriginalName(), $file->getMimeType(), $file->getSize());
        } finally {
            $this->streamRepository->save($stream);
        }
    }

    public function uploadByUrl(string $url, Uuid $userId, Uuid $streamId): void
    {
        /** @var ?User $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        if (null === $user) {
            throw new UnauthorizedHttpException();
        }
        
        /** @var ?Stream $stream */
        $stream = $this->streamRepository->findOneBy(['id' => $streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->getVideoByUrlProcessor)(
                new GetVideoByUrl($stream)
            );
        } catch (ProcessorException $exception) {
            $stream->markAsFailed();
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
