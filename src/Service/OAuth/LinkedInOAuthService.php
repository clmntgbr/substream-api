<?php

declare(strict_types=1);

namespace App\Service\OAuth;

use App\Core\Application\Command\CreateOrUpdateUserCommand;
use App\Core\Application\Command\CreateSocialAccountCommand;
use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use App\Dto\OAuth\LinkedIn\LinkedInAccount;
use App\Dto\OAuth\LinkedIn\LinkedInExchangeTokenPayload;
use App\Entity\User;
use App\Exception\OauthException;
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

        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateOrUpdateUserCommand(
            firstname: $userInfo->getFirstName(),
            picture: $userInfo->getProfilePicture(),
            lastname: $userInfo->getLastName(),
            email: $userInfo->getEmail(),
        ));

        if (null === $user->getLinkedInAccount()) {
            $this->commandBus->dispatch(new CreateSocialAccountCommand(
                provider: 'linkedin',
                accountId: $userInfo->getId(),
                email: $userInfo->getEmail(),
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
                throw new OauthException('Could not retrieve Linkedin account: an exception occurred during the request.');
            }

            return LinkedInAccount::fromArray($response->toArray());
        } catch (\Exception) {
            throw new OauthException('Could not retrieve Linkedin account: an exception occurred during the request.');
        }
    }
}
