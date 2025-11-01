<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class OptionNotFoundException extends BusinessException
{
    public function __construct(string $optionId = '')
    {
        parent::__construct(
            'Option not found',
            'error.option.not_found',
            ['optionId' => $optionId],
            Response::HTTP_NOT_FOUND
        );
    }
}
