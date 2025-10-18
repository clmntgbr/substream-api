<?php

namespace App\Controller\OAuth;

use App\Dto\OAuth\GoogleExchangeTokenPayload;
use App\Service\OAuth\GoogleOAuthService;
use App\Shared\Domain\Response\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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
    public function connect()
    {
        try {
            $data = $this->googleOAuthService->connect();
            return Response::successResponse($data);
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage());
        }
    }

    #[Route('/exchange-token', name: 'exchange_token', methods: ['POST'])]
    public function exchangeToken(#[MapRequestPayload()] GoogleExchangeTokenPayload $payload)
    {
        try {
            $user = $this->googleOAuthService->callback($payload);
            $token = $this->jwtManager->create($user);

            return new JsonResponse(
                data: [
                    'user' => $this->normalizer->normalize($user, null, ['groups' => ['user:read']]),
                    'token' => $token,
                ],
                status: JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage());
        }
    }
}