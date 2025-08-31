<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class UploadVideoByUrl
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 2048)]
        #[Assert\Regex(pattern: '/^https?:\/\/.+$/i')]
        public readonly string $url,
    ) {
    }
}