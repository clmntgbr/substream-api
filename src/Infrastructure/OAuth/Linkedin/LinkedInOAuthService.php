<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Linkedin;

use App\Application\SocialAccount\Command\CreateSocialAccountCommand;
use App\Application\User\Command\CreateOrUpdateUserCommand;
use App\Domain\OAuth\Gateway\OAuthServiceInterface;
use App\Domain\User\Entity\User;
use App\Domain\OAuth\Dto\ExchangeTokenPayloadInterface;
use App\Domain\OAuth\Dto\LinkedIn\LinkedInAccount;
use App\Domain\OAuth\Dto\LinkedIn\LinkedInExchangeTokenPayload;
use App\Shared\Application\Bus\CommandBusInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LinkedInOAuthService implements OAuthServiceInterface
{
    private const LINKEDIN_API_URL = 'https://api.linkedin.com';
    private const LINKEDIN_ACCOUNT = self::LINKEDIN_API_URL.'/v2/userinfo';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly CommandBusInterface $commandBus,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getScopes(): array
    {
        return [
            'profile',
            'email',
            'openid',
            'w_member_social',
        ];
    }

    public function connect(): array
    {
        $client = $this->clientRegistry->getClient('linkedin');
        $client->setAsStateless();

        $provider = $client->getOAuth2Provider();

        return [
            'url' => $provider->getAuthorizationUrl([
                'scope' => implode(',', $this->getScopes()),
            ]),
        ];
    }

    /**
     * @param LinkedInExchangeTokenPayload $payload
     */
    public function callback(ExchangeTokenPayloadInterface $payload): User
    {
        $client = $this->clientRegistry->getClient('linkedin');

        $provider = $client->getOAuth2Provider();

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $payload->getCode(),
        ]);

        $userInfo = $this->getResourceOwner($accessToken);

        $email = $userInfo->getEmail();
        if (null === $email) {
            throw new \RuntimeException('Email is required');
        }

        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateOrUpdateUserCommand(
            email: $email,
            firstname: $userInfo->getFirstName(),
            lastname: $userInfo->getLastName(),
            picture: $userInfo->getProfilePicture(),
        ));

        if (null === $user->getLinkedInAccount()) {
            $this->commandBus->dispatch(new CreateSocialAccountCommand(
                provider: 'linkedin',
                accountId: $userInfo->getId(),
                email: $email,
                user: $user,
            ));
        }

        return $user;
    }

    public function getResourceOwner(AccessTokenInterface $token): LinkedInAccount
    {
        $url = self::LINKEDIN_ACCOUNT;

        try {
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 30,
                'headers' => [
                    'Authorization' => 'Bearer '.$token->getToken(),
                    'Connection' => 'Keep-Alive',
                    'Accept' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                throw new \RuntimeException('Could not retrieve Linkedin account: an exception occurred during the request.');
            }

            return LinkedInAccount::fromArray($response->toArray());
        } catch (\Exception) {
            throw new \RuntimeException('Could not retrieve Linkedin account: an exception occurred during the request.');
        }
    }
}
