<?php

namespace App\Dto\OAuth;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterAccessToken implements AccessTokenInterface
{
    public function __construct(
        public string $oauthToken,
        public string $oauthTokenSecret,
    ) {
    }

    public static function fromString(string $responseString): self
    {
        parse_str($responseString, $params);

        return new self(
            oauthToken: $params['oauth_token'] ?? '',
            oauthTokenSecret: $params['oauth_token_secret'] ?? ''
        );
    }
}