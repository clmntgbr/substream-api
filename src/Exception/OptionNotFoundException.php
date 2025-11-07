<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class OptionNotFoundException extends BusinessException
{
    public function __construct(string $optionId = '')
    {
        parent::__construct(
            'The requested option could not be found. Please verify the option identifier and try again.',
            ErrorKeyEnum::OPTION_NOT_FOUND->value,
            ['optionId' => $optionId],
            Response::HTTP_NOT_FOUND
        );
    }
}
