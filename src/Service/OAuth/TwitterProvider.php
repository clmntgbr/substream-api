<?php

namespace App\Service\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;

class TwitterProvider extends GenericProvider
{
    public function __construct(array $options = [], array $collaborators = [])
    {
        // Force HTTP Basic Auth for Twitter (required by Twitter OAuth2)
        $collaborators['optionProvider'] = new HttpBasicAuthOptionProvider();
        
        parent::__construct($options, $collaborators);
    }
}

