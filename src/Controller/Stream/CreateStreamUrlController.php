<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Dto\CreateStreamUrlPayload;
use App\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class CreateStreamUrlController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
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

        return new JsonResponse([
            'success' => true,
            'data' => [
                'streamId' => $createStreamModel->streamId,
            ],
        ]);
    }
}
