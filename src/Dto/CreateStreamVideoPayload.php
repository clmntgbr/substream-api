<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamVideoPayload
{
    public function __construct(
        #[Assert\NotBlank(message: 'error.validation.option_id.required')]
        #[Assert\Uuid(message: 'error.validation.option_id.invalid')]
        #[Assert\Length(max: 36)]
        private Uuid $optionId,
        #[Assert\NotBlank(message: 'error.validation.duration.required')]
        #[Assert\Type('string', message: 'error.validation.duration.invalid')]
        #[Assert\Positive(message: 'error.validation.duration.must_be_positive')]
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
