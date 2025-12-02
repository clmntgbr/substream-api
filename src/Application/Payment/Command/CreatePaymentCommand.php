<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class CreatePaymentCommand implements AsynchronousInterface
{
    public function __construct(
        private string $customerId,
        private string $subscriptionId,
        private string $invoiceId,
        private string $invoiceUrl,
        private string $paymentStatus,
        private string $currency,
        private int $amount,
        private ?string $stripePriceId = null,
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

    public function getStripePriceId(): ?string
    {
        return $this->stripePriceId;
    }

    public function getStamps(): array
    {
        return [
            new DelayStamp(15 * 1000),
        ];
    }
}
