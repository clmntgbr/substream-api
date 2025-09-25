<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Options;
use App\Entity\Stream;
use App\Entity\User;
use App\Enum\StreamStatusEnum;
use App\Repository\StreamRepository;
use Doctrine\ORM\EntityManagerInterface;

class StreamService
{
    public function __construct(
        private StreamRepository $streamRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function createStream(
        string $fileName,
        string $originalFileName,
        string $mimeType,
        int $size,
        string $url,
        User $user,
        Options $options
    ): Stream {
        $stream = new Stream();
        $stream->setFileName($fileName);
        $stream->setOriginalFileName($originalFileName);
        $stream->setMimeType($mimeType);
        $stream->setSize($size);
        $stream->setUrl($url);
        $stream->setUser($user);
        $stream->setOptions($options);
        $stream->setStatus(StreamStatusEnum::UPLOADING->value);
        $stream->setStatuses([StreamStatusEnum::UPLOADING->value]);

        $this->entityManager->persist($stream);
        $this->entityManager->flush();

        return $stream;
    }

    public function getStreamById(\Symfony\Component\Uid\Uuid $streamId): ?Stream
    {
        return $this->streamRepository->find($streamId);
    }

    public function getUserStreams(User $user): array
    {
        return $this->streamRepository->findBy(['user' => $user]);
    }
}
