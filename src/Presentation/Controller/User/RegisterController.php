<?php

declare(strict_types=1);

namespace App\Presentation\Controller\User;

use App\Application\User\Command\CreateUserCommand;
use App\Domain\User\Dto\RegisterPayload;
use App\Domain\User\Entity\User;
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
            'user' => $this->normalizer->normalize($user, null, ['groups' => User::GROUP_USER_READ]),
            'token' => $token,
        ]);
    }
}
