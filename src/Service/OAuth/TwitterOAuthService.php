<?php

namespace App\Service\OAuth;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Core\Application\Command\CreateUserCommand;
use App\Dto\OAuth\AccessTokenInterface;
use App\Dto\OAuth\CallbackPayloadInterface;
use App\Dto\OAuth\TwitterAccessToken;
use App\Dto\OAuth\TwitterAccount;
use App\Dto\OAuth\TwitterCallbackPayload;
use App\Dto\OAuth\TwitterRequestToken;
use App\Entity\User;
use App\Exception\OauthException;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\CommandBus;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitterOAuthService implements OAuthServiceInterface
{
    private const TWITTER_API_URL = 'https://api.x.com';
    private const TWITTER_CONNECT_URL = self::TWITTER_API_URL.'/oauth/authenticate';
    private const TWITTER_ACCESS_TOKEN = self::TWITTER_API_URL.'/oauth/access_token';

    public function __construct(
        private UserRepository $userRepository,
        private readonly DenormalizerInterface $denormalizer,
        private readonly HttpClientInterface $httpClient,
        private readonly CommandBusInterface $commandBus,
        private ?TwitterOAuth $twitterOAuth = null,
        private readonly string $twitterApiKey,
        private readonly string $twitterApiSecret,
        private readonly string $twitterClientId,
        private readonly string $backendUrl,
        private readonly string $oauthState,
    ) {
        $this->twitterOAuth = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret);
    }

    public function connect(): string
    {
        try {
            $requestToken = $this->getRequestToken();
        } catch (\Exception) {
            throw new OauthException('Could not obtain request token from Twitter');
        }

        $params = [
            'oauth_token' => $requestToken->oauthToken,
        ];

        return self::TWITTER_CONNECT_URL.'?'.http_build_query($params);
    }

    public function getScopes(): array
    {
        return [
            'users.read', 
            'users.email',
            'created_at',
            'description',
            'entities',
            'id',
            'location',
            'most_recent_tweet_id',
            'name',
            'pinned_tweet_id',
            'profile_image_url',
            'protected',
            'public_metrics',
            'url',
            'username',
            'verified',
            'verified_type',
            'withheld',
        ];
    }

    private function getRequestToken(): TwitterRequestToken
    {
        $url = $this->backendUrl.OAuthServiceInterface::TWITTER_CALLBACK_URL.'?'.http_build_query([
            'state' => $this->oauthState,
        ]);

        try {
            $response = $this->twitterOAuth->oauth('oauth/request_token', [
                'oauth_callback' => $url,
            ]);

            return $this->denormalizer->denormalize($response, TwitterRequestToken::class);
        } catch (\Exception) {
            throw new OauthException('Could not obtain request token from Twitter');
        }
    }

    /**
     * @param TwitterCallbackPayload $payload
     */
    public function callback(CallbackPayloadInterface $payload): void
    {
        if ($payload->getState() !== $this->oauthState) {
            throw new OauthException('Invalid state');
        }

        $accessToken = $this->getAccessToken($payload);
        $account = $this->getAccountInfo($accessToken);

        // $this->commandBus->dispatch(new CreateUserCommand(
        //     email: $account->email,
        //     password: $account->password,
        //     name: $account->name,
        //     username: $account->username,
        //     profileImageUrl: $account->profileImageUrl,
        // ));

        dd($account);
    }

    /**
     * @param TwitterCallbackPayload $payload
     */
    public function getAccessToken(CallbackPayloadInterface $payload): TwitterAccessToken
    {
        $params = [
            'oauth_token' => $payload->getOauthToken(),
            'oauth_verifier' => $payload->getOauthVerifier(),
        ];

        $url = self::TWITTER_ACCESS_TOKEN.'?'.http_build_query($params);

        try {
            $response = $this->httpClient->request('POST', $url);

            $statusCode = $response->getStatusCode();

            if (200 !== $statusCode) {
                throw new OauthException("Twitter API error: received status code {$statusCode} when requesting access token.", $statusCode);
            }

            return TwitterAccessToken::fromString($response->getContent());
        } catch (\Exception) {
            throw new OauthException('Could not retrieve access token from Twitter API: an exception occurred during the request.');
        }
    }

    /**
     * @param TwitterAccessToken $token
     */
    public function getAccountInfo(AccessTokenInterface $token): TwitterAccount
    {
        try {
            $twitterOAuth = new TwitterOAuth($this->twitterApiKey, $this->twitterApiSecret, $token->oauthToken, $token->oauthTokenSecret);
            $twitterOAuth->setApiVersion('2');

            $response = $twitterOAuth->get('users/me', [
                'expansions' => ['pinned_tweet_id'],
                'user.fields' => implode(',', $this->getScopes()),
            ]);

            $response = $response->data ?? $response;
            dd($response);

            return $this->denormalizer->denormalize($response, TwitterAccount::class);
        } catch (\Exception) {
            throw new OauthException('Could not retrieve Twitter accounts: an exception occurred during the request.');
        }
    }
}