<?php

declare(strict_types=1);

namespace App\Enum;

enum PlanTypeEnum: string
{
    case FREE = 'plan_free';
    case STARTER_MONTHLY = 'plan_starter_monthly';
    case STARTER_YEARLY = 'plan_starter_yearly';
    case PRO_MONTHLY = 'plan_pro_monthly';
    case PRO_YEARLY = 'plan_pro_yearly';
    case BUSINESS_MONTHLY = 'plan_business_monthly';
    case BUSINESS_YEARLY = 'plan_business_yearly';
}
