<?php

namespace App\Dto;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamVideoPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private Uuid $optionId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $duration,
    ) {
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }

    public function getDuration(): string
    {
        return $this->duration;
    }
}
