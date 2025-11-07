<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class PlanNotFoundException extends BusinessException
{
    public function __construct(string $optionId = '')
    {
        parent::__construct(
            'The requested option could not be found. Please verify the option identifier and try again.',
            TranslatableKeyEnum::OPTION_NOT_FOUND->value,
            ['optionId' => $optionId],
            Response::HTTP_NOT_FOUND
        );
    }
}
