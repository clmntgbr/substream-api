<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Core\Domain\User\Entity\User;
use App\Dto\CreateStreamUrlPayload;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class CreateStreamUrlController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly StreamRepository $streamRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload()] CreateStreamUrlPayload $payload,
        #[CurrentUser] User $user,
    ): JsonResponse {
        $createStreamModel = $this->commandBus->dispatch(
            new CreateStreamUrlCommand(
                name: $payload->getName(),
                url: $payload->getUrl(),
                thumbnailFile: $payload->getThumbnailFile(),
                optionId: $payload->getOptionId(),
                user: $user,
            ),
        );

        $stream = $this->streamRepository->find($createStreamModel->streamId);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'stream' => $this->normalizer->normalize($stream, null, ['groups' => ['stream:read', 'option:read']]),
            ],
        ]);
    }
}
