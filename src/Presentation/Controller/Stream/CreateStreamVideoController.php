<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Stream;

use App\Application\Stream\Command\CreateStreamVideoCommand;
use App\Domain\Stream\Dto\CreateStreamVideoPayload;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\User\Entity\User;
use App\Infrastructure\Validation\UploadedFileValidator;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Throwable;

#[AsController]
class CreateStreamVideoController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly UploadedFileValidator $fileValidator,
        private readonly StreamRepository $streamRepository,
        private readonly NormalizerInterface $normalizer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        #[MapUploadedFile]
        UploadedFile $video,
        #[MapUploadedFile]
        UploadedFile $thumbnail,
        #[CurrentUser]
        User $user,
        #[MapRequestPayload()]
        CreateStreamVideoPayload $payload,
    ): JsonResponse {
        try {
            $this->fileValidator->validateVideo($video);
            $this->fileValidator->validateThumbnail($thumbnail);

            /** @var Stream $stream */
            $stream = $this->commandBus->dispatch(
                new CreateStreamVideoCommand(
                    file: $video,
                    thumbnail: $thumbnail,
                    duration: $payload->getDuration(),
                    optionId: $payload->getOptionId(),
                    user: $user,
                ),
            );

            $stream = $this->streamRepository->find($stream->getId());

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'stream' => $this->normalizer->normalize($stream, null, ['groups' => ['stream:read', 'option:read']]),
                ],
            ]);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());

            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
