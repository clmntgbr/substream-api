<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionNotFoundException extends BusinessException
{
    public function __construct(string $subscriptionId = '')
    {
        parent::__construct(
            'The requested subscription could not be found. Please verify the subscription identifier and try again.',
            TranslatableKeyEnum::SUBSCRIPTION_NOT_FOUND->value,
            ['subscriptionId' => $subscriptionId],
            Response::HTTP_NOT_FOUND
        );
    }
}
