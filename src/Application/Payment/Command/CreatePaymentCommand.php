<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousPriorityInterface;

final class CreatePaymentCommand implements AsynchronousPriorityInterface
{
    public function __construct(
        private string $customerId,
        private string $subscriptionId,
        private string $invoiceId,
        private string $invoiceUrl,
        private string $paymentStatus,
        private string $currency,
        private int $amount,
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

    public function getInvoiceUrl(): string
    {
        return $this->invoiceUrl;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getStamps(): array
    {
        return [];
    }
}
