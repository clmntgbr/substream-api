<?php

namespace App\Controller\OAuth;

use App\Dto\OAuth\LinkedIn\LinkedInExchangeTokenPayload;
use App\Service\OAuth\LinkedInOAuthService;
use App\Shared\Domain\Response\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/oauth/linkedin', name: 'oauth_linkedin_')]
class LinkedInOauthController extends AbstractController
{
    public function __construct(
        private LinkedInOAuthService $linkedInOAuthService,
        private JWTTokenManagerInterface $jwtManager,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[Route('/connect', name: 'connect', methods: ['GET'])]
    public function connect()
    {
        try {
            $data = $this->linkedInOAuthService->connect();

            return Response::successResponse($data);
        } catch (\Exception) {
            return Response::errorResponse('Could not connect to LinkedIn');
        }
    }

    #[Route('/exchange-token', name: 'exchange_token', methods: ['POST'])]
    public function exchangeToken(#[MapRequestPayload()] LinkedInExchangeTokenPayload $payload)
    {
        try {
            $user = $this->linkedInOAuthService->callback($payload);
            $token = $this->jwtManager->create($user);

            return new JsonResponse(
                data: [
                    'user' => $this->normalizer->normalize($user, null, ['groups' => ['user:read']]),
                    'token' => $token,
                ],
                status: JsonResponse::HTTP_OK
            );
        } catch (\Exception) {
            return Response::errorResponse('Could not exchange token');
        }
    }
}
