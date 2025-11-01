<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Core\Application\Command\CreateStreamVideoCommand;
use App\Dto\CreateStreamVideoPayload;
use App\Entity\User;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Validator\UploadedFileValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class CreateStreamVideoController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly UploadedFileValidator $fileValidator,
        private readonly StreamRepository $streamRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(
        #[MapUploadedFile] UploadedFile $video,
        #[MapUploadedFile] UploadedFile $thumbnail,
        #[CurrentUser] User $user,
        #[MapRequestPayload()] CreateStreamVideoPayload $payload,
    ): JsonResponse {
        // Validate files before processing
        $this->fileValidator->validateVideo($video);
        $this->fileValidator->validateThumbnail($thumbnail);

        $createStreamModel = $this->commandBus->dispatch(
            new CreateStreamVideoCommand(
                file: $video,
                thumbnail: $thumbnail,
                duration: $payload->getDuration(),
                optionId: $payload->getOptionId(),
                user: $user,
            ),
        );

        // Return the complete stream object so frontend doesn't need to refresh
        $stream = $this->streamRepository->find($createStreamModel->streamId);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'stream' => $this->normalizer->normalize($stream, null, ['groups' => ['stream:read', 'option:read']]),
            ],
        ]);
    }
}
