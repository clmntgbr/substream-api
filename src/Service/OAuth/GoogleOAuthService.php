<?php

declare(strict_types=1);

namespace App\Service\OAuth;

use App\Core\Application\Command\CreateOrUpdateUserCommand;
use App\Core\Application\Command\CreateSocialAccountCommand;
use App\Core\Domain\User\Entity\User;
use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use App\Dto\OAuth\Google\GoogleAccount;
use App\Dto\OAuth\Google\GoogleExchangeTokenPayload;
use App\Shared\Application\Bus\CommandBusInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Token\AccessToken;

class GoogleOAuthService implements OAuthServiceInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly CommandBusInterface $commandBus,
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
    public function callback(ExchangeTokenPayloadInterface $payload): User
    {
        $client = $this->clientRegistry->getClient('google');

        $provider = $client->getOAuth2Provider();

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $payload->getCode(),
        ]);

        /** @var AccessToken $accessToken */
        $userInfo = $provider->getResourceOwner($accessToken);
        $account = GoogleAccount::fromArray($userInfo->toArray());

        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateOrUpdateUserCommand(
            email: $account->getEmail(),
            firstname: $account->getGivenName(),
            lastname: $account->getFamilyName(),
            picture: $account->getPicture(),
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
