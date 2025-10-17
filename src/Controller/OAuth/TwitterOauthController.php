<?php

namespace App\Controller\OAuth;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Dto\OAuth\TwitterCallbackPayload;
use App\Entity\User;
use App\Service\OAuth\TwitterOAuthService;
use App\Shared\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
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
        return Response::successResponse(
            ['url' => $this->twitterOAuthService->connect()],
        );
    }

    #[Route('/callback', name: 'callback', methods: ['GET'])]
    public function callback(#[MapQueryString()] TwitterCallbackPayload $payload)
    {
        $this->twitterOAuthService->callback($payload);
        dd($payload);
    }
}