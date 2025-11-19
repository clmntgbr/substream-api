<?php

declare(strict_types=1);

namespace App\Domain\Stream\Dto;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamVideoPayload
{
    public function __construct(
        #[Assert\NotBlank()]
        #[Assert\Uuid()]
        private Uuid $optionId,
        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Positive()]
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
