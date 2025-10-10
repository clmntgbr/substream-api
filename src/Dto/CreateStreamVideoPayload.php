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
    ) {
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }
}
