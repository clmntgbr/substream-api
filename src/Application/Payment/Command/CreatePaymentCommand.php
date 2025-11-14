<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\SynchronousInterface;

final class CreatePaymentCommand implements SynchronousInterface
{
    public function __construct(
        private string $customerId,
        private string $subscriptionId,
        private string $invoiceId,
        private string $paymentStatus,
        private string $currency,
        private string $amount,
    ) {
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function getInvoiceId(): string
    {
        return $this->invoiceId;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }
}
