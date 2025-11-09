<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;

final class CreateStripePaymentCommand extends CommandAbstract implements SyncCommandInterface
{
    public function __construct(
        private string $checkoutSessionId,
        private string $customerId,
        private string $subscriptionId,
        private string $invoiceId,
        private string $paymentStatus,
        private string $currency,
        private string $amount,
    ) {
    }

    public function getCheckoutSessionId(): string
    {
        return $this->checkoutSessionId;
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
