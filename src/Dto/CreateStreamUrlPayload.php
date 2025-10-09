<?php

namespace App\Dto;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamUrlPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 2048)]
        #[Assert\Regex(pattern: '/^https?:\/\/.+$/i', message: 'Invalid URL')]
        private string $url,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private Uuid $optionId,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }
}
