<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Github;

use App\Application\SocialAccount\Command\CreateSocialAccountCommand;
use App\Application\User\Command\CreateOrUpdateUserCommand;
use App\Domain\OAuth\Dto\ExchangeTokenPayloadInterface;
use App\Domain\OAuth\Dto\Github\GithubAccount;
use App\Domain\OAuth\Dto\Github\GithubExchangeTokenPayload;
use App\Domain\OAuth\Gateway\OAuthServiceInterface;
use App\Domain\OAuth\ValueObject\CodeChallenge;
use App\Domain\User\Entity\User;
use App\Shared\Application\Bus\CommandBusInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubOAuthService implements OAuthServiceInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly CommandBusInterface $commandBus,
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function connect(): array
    {
        $client = $this->clientRegistry->getClient('github');
        $client->setAsStateless();

        $codeChallenge = CodeChallenge::generate();

        return [
            'url' => $client->getOAuth2Provider()->getAuthorizationUrl([
                'code_challenge' => $codeChallenge->getCodeChallenge(),
                'code_challenge_method' => $codeChallenge->getCodeChallengeMethod(),
                'scope' => ['user:email'], // Request email scope to access user emails
            ]),
            'code_verifier' => $codeChallenge->getCodeVerifier(),
        ];
    }

    /**
     * @param GithubExchangeTokenPayload $payload
     */
    public function callback(ExchangeTokenPayloadInterface $payload): User
    {
        $client = $this->clientRegistry->getClient('github');

        $provider = $client->getOAuth2Provider();

        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $payload->getCode(),
            'code_verifier' => $payload->getCodeVerifier(),
        ]);

        /** @var AccessToken $accessToken */
        $userInfo = $provider->getResourceOwner($accessToken);
        $email = $this->fetchPrimaryEmail($accessToken->getToken());

        $account = GithubAccount::fromArray($userInfo->toArray());

        if (null === $email) {
            $email = $account->getEmail();
        }

        if (null === $email) {
            throw new \RuntimeException('Email is required');
        }

        /** @var User $user */
        $user = $this->commandBus->dispatch(new CreateOrUpdateUserCommand(
            email: $email,
            firstname: $account->getName(),
            lastname: null,
            picture: $account->getAvatarUrl(),
        ));

        if (null === $user->getGithubAccount()) {
            $this->commandBus->dispatch(new CreateSocialAccountCommand(
                provider: 'github',
                accountId: $account->getId(),
                email: $email,
                user: $user,
            ));
        }

        return $user;
    }

    private function fetchPrimaryEmail(string $accessToken): ?string
    {
        try {
            $response = $this->httpClient->request('GET', 'https://api.github.com/user/emails', [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                ],
            ]);

            $emails = $response->toArray();

            $realEmails = array_filter($emails, function ($email) {
                return !str_contains($email['email'], '@users.noreply.github.com');
            });

            foreach ($realEmails as $emailData) {
                if (($emailData['primary'] ?? false) && ($emailData['verified'] ?? false)) {
                    return $emailData['email'];
                }
            }

            foreach ($realEmails as $emailData) {
                if ($emailData['verified'] ?? false) {
                    return $emailData['email'];
                }
            }

            if (!empty($realEmails)) {
                return reset($realEmails)['email'];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
