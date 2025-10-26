<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class SearchRequestDto
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public int $page = 1,

        #[Assert\Positive]
        public int $itemsPerPage = 15,
    ) {
    }

    public static function fromQuery(array $query): self
    {
        return new self(
            page: (int) ($query['page'] ?? 1),
            itemsPerPage: (int) ($query['itemsPerPage'] ?? 15),
        );
    }
}