<?php

declare(strict_types=1);

namespace App\Core\Presentation\Controller\OAuth;

use App\Core\Domain\User\Entity\User;
use App\Core\Infrastructure\OAuth\Google\GoogleOAuthService;
use App\Dto\OAuth\Google\GoogleExchangeTokenPayload;
use App\Shared\Domain\Response\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/oauth/google', name: 'oauth_google_')]
class GoogleOauthController extends AbstractController
{
    public function __construct(
        private GoogleOAuthService $googleOAuthService,
        private JWTTokenManagerInterface $jwtManager,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[Route('/connect', name: 'connect', methods: ['GET'])]
    public function connect(): JsonResponse
    {
        try {
            $data = $this->googleOAuthService->connect();

            return Response::successResponse($data);
        } catch (\Exception) {
            return Response::errorResponse('Could not connect to Google');
        }
    }

    #[Route('/exchange-token', name: 'exchange_token', methods: ['POST'])]
    public function exchangeToken(#[MapRequestPayload()] GoogleExchangeTokenPayload $payload): JsonResponse
    {
        try {
            $user = $this->googleOAuthService->callback($payload);
            $token = $this->jwtManager->create($user);

            return new JsonResponse(
                data: [
                    'user' => $this->normalizer->normalize($user, null, ['groups' => User::GROUP_USER_READ]),
                    'token' => $token,
                ],
                status: JsonResponse::HTTP_OK
            );
        } catch (\Exception) {
            return Response::errorResponse('Could not exchange token');
        }
    }
}
