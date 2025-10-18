<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\TwitterExchangeTokenPayload;
use App\Exception\OauthException;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwitterOAuthService
{
    public function __construct(
        private UserRepository $userRepository,
        private readonly ClientRegistry $clientRegistry,
        private readonly HttpClientInterface $httpClient,
        private readonly string $frontendRedirectUrl,
    ) {
    }

    public function connect(): array
    {
        $codeVerifier = $this->generateCodeVerifier();
        $codeChallenge = $this->generateCodeChallenge($codeVerifier);
        
        $client = $this->clientRegistry->getClient('twitter');
        $client->setAsStateless();
        
        $provider = $client->getOAuth2Provider();
        
        // Override redirect_uri via reflection since it's protected
        $reflection = new \ReflectionClass($provider);
        $property = $reflection->getProperty('redirectUri');
        $property->setAccessible(true);
        $property->setValue($provider, $this->frontendRedirectUrl);
        
        $url = $provider->getAuthorizationUrl([
            'scope' => ['tweet.read', 'users.read', 'offline.access'],
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
        
        return [
            'url' => $url,
            'code_verifier' => $codeVerifier,
        ];
    }
    
    private function generateCodeVerifier(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
    
    private function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }

    /**
     * For API mode: Frontend must send code_verifier in the request
     */
    public function callback(TwitterExchangeTokenPayload $payload): array
    {
        try {
            $code = $payload->getCode();
            $codeVerifier = $payload->getCodeVerifier();
            
            // Get access token with code_verifier
            $client = $this->clientRegistry->getClient('twitter');
            $provider = $client->getOAuth2Provider();
            
            // CRITICAL: redirect_uri must be exactly the same as in connect()
            $reflection = new \ReflectionClass($provider);
            $property = $reflection->getProperty('redirectUri');
            $property->setAccessible(true);
            $property->setValue($provider, $this->frontendRedirectUrl);
            
            // Debug: log what we're sending
            error_log('=== Twitter OAuth Debug ===');
            error_log('Code: ' . $code);
            error_log('Code Verifier: ' . $codeVerifier);
            error_log('Redirect URI: ' . $this->frontendRedirectUrl);
            
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code,
                'code_verifier' => $codeVerifier,
            ]);
            
            error_log('✅ Access token obtained: ' . substr($accessToken->getToken(), 0, 20) . '...');
            error_log('Refresh token: ' . ($accessToken->getRefreshToken() ? 'Yes' : 'No'));
            
            // Get user info from Twitter
            try {
                $response = $this->httpClient->request('GET', 'https://api.twitter.com/2/users/me', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken->getToken(),
                    ],
                    'query' => [
                        'user.fields' => 'id,name,username,profile_image_url,description,created_at,verified,verified_type',
                    ],
                ]);
                
                $userData = json_decode($response->getContent(), true);
                error_log('✅ User data: ' . json_encode($userData));
            } catch (\Exception $userError) {
                error_log('❌ Failed to get user info: ' . $userError->getMessage());
                // Twitter rate limit - return token anyway, fetch user later
                $userData = ['data' => [
                    'id' => null,
                    'username' => null,
                    'name' => 'Twitter User (rate limited)',
                    'note' => 'User info can be fetched later with the access token'
                ]];
            }
            
            error_log('✅ User data: ' . json_encode($userData));
            error_log('✅ Access token: ' . $accessToken->getToken());
            error_log('✅ Refresh token: ' . $accessToken->getRefreshToken());
            error_log('✅ Expires in: ' . $accessToken->getExpires());
            
            return [
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires_in' => $accessToken->getExpires(),
                'user' => $userData['data'] ?? null,
            ];
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            error_log('Twitter API Error: ' . json_encode($e->getResponseBody()));
            throw new OauthException('Failed to exchange token: ' . json_encode($e->getResponseBody()));
        } catch (\Exception $e) {
            error_log('General Error: ' . $e->getMessage());
            throw new OauthException('Failed to exchange token: '.$e->getMessage());
        }
    }
}