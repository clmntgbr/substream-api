<?php

declare(strict_types=1);

namespace App\Domain\Payment\Dto;

class Preview
{
    public function __construct(
        public readonly float $amountDue,
        public readonly float $credit,
        public readonly float $debit,
        public readonly string $currency,
        public readonly string $nextBillingDate,
    ) {
    }
}
