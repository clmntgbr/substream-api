<?php

namespace App\Controller\User;

use App\Core\Application\Command\CreateUserCommand;
use App\Dto\RegisterPayload;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Domain\Response\Response;
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
        private CommandBusInterface $commandBus,
        private readonly NormalizerInterface $normalizer,
        private readonly UserRepository $userRepository,
        private JWTTokenManagerInterface $jwtManager,
    ) {
    }

    public function __invoke(#[MapRequestPayload()] RegisterPayload $payload): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $this->commandBus->dispatch(new CreateUserCommand(
                firstname: $payload->getFirstname(),
                lastname: $payload->getLastname(),
                email: $payload->getEmail(),
                plainPassword: $payload->getPlainPassword(),
            ));

            $token = $this->jwtManager->create($user);

            return new JsonResponse(
                data: [
                    'user' => $this->normalizer->normalize($user, null, ['groups' => ['user:read']]),
                    'token' => $token,
                ],
                status: JsonResponse::HTTP_OK
            );
        } catch (\Exception $exception) {
            return Response::errorResponse($exception->getMessage());
        }
    }
}
