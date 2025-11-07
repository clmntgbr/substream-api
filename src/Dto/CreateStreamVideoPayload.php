<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamVideoPayload
{
    public function __construct(
        #[Assert\NotBlank(message: ErrorKeyEnum::VALIDATION_OPTION_ID_REQUIRED->value)]
        #[Assert\Uuid(message: ErrorKeyEnum::VALIDATION_OPTION_ID_INVALID->value)]
        #[Assert\Length(max: 36)]
        private Uuid $optionId,
        #[Assert\NotBlank(message: ErrorKeyEnum::VALIDATION_DURATION_REQUIRED->value)]
        #[Assert\Type('string', message: ErrorKeyEnum::VALIDATION_DURATION_INVALID->value)]
        #[Assert\Positive(message: ErrorKeyEnum::VALIDATION_DURATION_MUST_BE_POSITIVE->value)]
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
