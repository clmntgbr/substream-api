<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Core\Application\Command\CreateUserCommand;
use App\Dto\RegisterPayload;
use App\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsController]
class RegisterController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly NormalizerInterface $normalizer,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly string $backendUrl,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload()] RegisterPayload $payload,
    ): JsonResponse {
        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateUserCommand(
            email: $payload->getEmail(),
            plainPassword: $payload->getPlainPassword(),
            firstname: $payload->getFirstname(),
            lastname: $payload->getLastname(),
            picture: $this->backendUrl.'/uploads/avatar.jpg',
        ));

        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'success' => true,
            'data' => [
                'user' => $this->normalizer->normalize($user, null, ['groups' => ['user:read']]),
                'token' => $token,
            ],
        ]);
    }
}
