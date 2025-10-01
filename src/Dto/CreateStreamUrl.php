<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamUrl
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 2048)]
        #[Assert\Regex(pattern: '/^https?:\/\/.+$/i', message: 'Invalid URL')]
        private string $url,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
