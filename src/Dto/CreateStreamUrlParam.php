<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamUrlParam
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 2048)]
        #[Assert\Regex(pattern: '/^https?:\/\/.+$/i', message: 'Invalid URL')]
        public string $url,
    ) {
    }
}
