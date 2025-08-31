<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UploadVideoByUrl
{
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Assert\Length(max: 2048)]
    #[Assert\Regex(pattern: '/^https?:\/\/.+$/i')]
    public function __construct(
        public readonly string $url,
    ) {
    }
}