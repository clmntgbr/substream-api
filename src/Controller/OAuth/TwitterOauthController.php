<?php

namespace App\Controller\OAuth;

use App\Dto\OAuth\TwitterExchangeTokenPayload;
use App\Service\OAuth\TwitterOAuthService;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/oauth/twitter', name: 'oauth_twitter_')]  
class TwitterOauthController extends AbstractController
{
    public function __construct(
        private TwitterOAuthService $twitterOAuthService,
    ) {
    }

    #[Route('/connect', name: 'connect', methods: ['GET'])]
    public function connect()
    {
        try {
            $data = $this->twitterOAuthService->connect();
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage());
        }

        return Response::successResponse($data);
    }

    #[Route('/exchange-token', name: 'exchange_token', methods: ['POST'])]
    public function exchangeToken(#[MapRequestPayload()] TwitterExchangeTokenPayload $payload)
    {
        try {
            $data = $this->twitterOAuthService->callback($payload);
            return Response::successResponse($data);
        } catch (\Exception $e) {
            return Response::errorResponse($e->getMessage());
        }
    }
}