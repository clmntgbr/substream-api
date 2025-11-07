<?php

declare(strict_types=1);

namespace App\Controller\OAuth;

use App\Dto\OAuth\Github\GithubExchangeTokenPayload;
use App\Entity\User;
use App\Service\OAuth\GithubOAuthService;
use App\Shared\Domain\Response\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/oauth/github', name: 'oauth_github_')]
class GithubOauthController extends AbstractController
{
    public function __construct(
        private GithubOAuthService $githubOAuthService,
        private JWTTokenManagerInterface $jwtManager,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    #[Route('/connect', name: 'connect', methods: ['GET'])]
    public function connect(): JsonResponse
    {
        try {
            $data = $this->githubOAuthService->connect();

            return Response::successResponse($data);
        } catch (\Exception) {
            return Response::errorResponse('Could not connect to Github');
        }
    }

    #[Route('/exchange-token', name: 'exchange_token', methods: ['POST'])]
    public function exchangeToken(#[MapRequestPayload()] GithubExchangeTokenPayload $payload): JsonResponse
    {
        try {
            $user = $this->githubOAuthService->callback($payload);
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
