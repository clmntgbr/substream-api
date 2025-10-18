<?php

namespace App\Service\OAuth;

use App\Core\Application\Command\CreateOrUpdateUserCommand;
use App\Core\Application\Command\CreateSocialAccountCommand;
use App\Core\Application\Command\CreateUserCommand;
use App\Dto\OAuth\CallbackPayloadInterface;
use App\Dto\OAuth\GoogleAccount;
use App\Dto\OAuth\GoogleExchangeTokenPayload;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Infrastructure\Bus\CommandBus;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class GoogleOAuthService
{
    private const YOUTUBE_OAUTH_URL = 'https://accounts.google.com/o/oauth2/v2';
    private const YOUTUBE_CONNECT_URL = self::YOUTUBE_OAUTH_URL.'/auth';

    public function __construct(
        private UserRepository $userRepository,
        private readonly ClientRegistry $clientRegistry,
        private readonly HttpClientInterface $httpClient,
        private readonly CommandBusInterface $commandBus,
        private readonly string $frontendRedirectUrl,

    ) {
    }

    public function connect(): array
    {
        $client = $this->clientRegistry->getClient('google');
        $client->setAsStateless();
        
        return [
            'url' => $client->getOAuth2Provider()->getAuthorizationUrl(),
        ];
    }

    /**
     * @param GoogleExchangeTokenPayload $payload
     */
    public function callback(CallbackPayloadInterface $payload): User
    {
        $client = $this->clientRegistry->getClient('google');

        $provider = $client->getOAuth2Provider();
        
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $payload->getCode(),
        ]);

        $userInfo = $provider->getResourceOwner($accessToken);
        $account = GoogleAccount::fromArray($userInfo->toArray());

        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateOrUpdateUserCommand(
            firstname: $account->getGivenName(),
            lastname: $account->getFamilyName(),
            email: $account->getEmail(),
        ));

        if (null === $user->getGoogleAccount()) {
            $this->commandBus->dispatch(new CreateSocialAccountCommand(
                provider: 'google',
                accountId: $account->getId(),
                email: $account->getEmail(),
                user: $user,
            ));
        }

        return $user;
    }
}