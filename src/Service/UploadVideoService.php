<?php

namespace App\Service;

use App\Client\Processor\GetVideoByUrlProcessorInterface;
use App\Dto\Processor\GetVideoByUrl;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Exception\UnauthorizedHttpException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;
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
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (null === $user) {
            throw new UnauthorizedHttpException();
        }

        $stream = $this->streamRepository->findOneBy(['id' => $streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $videoFileName = $streamId.'.'.$file->guessExtension();
            $path = $streamId.'/'.$videoFileName;

            $handle = fopen($file->getPathname(), 'r');

            $this->awsStorage->writeStream($path, $handle, [
                'visibility' => 'public',
                'mimetype' => $file->getMimeType(),
            ]);

            if (is_resource($handle)) {
                fclose($handle);
            }

            $stream->updateStream($videoFileName, $file->getClientOriginalName(), $file->getMimeType(), $file->getSize());
        } catch (\Exception $_) {
            $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
            $stream->setOriginalName($file->getClientOriginalName());
            $stream->setMimeType($file->getMimeType());
            $stream->setSize($file->getSize());
        } finally {
            $this->streamRepository->save($stream);
        }
    }

    public function uploadByUrl(string $url, Uuid $userId, Uuid $streamId): void
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (null === $user) {
            throw new UnauthorizedHttpException();
        }

        $stream = $this->streamRepository->findOneBy(['id' => $streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->getVideoByUrlProcessor)(
                new GetVideoByUrl($stream)
            );
        } catch (ProcessorException $exception) {
            $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
