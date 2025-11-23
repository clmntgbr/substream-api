<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousPriorityInterface;

final class CreatePaymentFailedCommand implements AsynchronousPriorityInterface
{
    public function __construct(
        private string $customerId,
        private string $subscriptionId,
        private string $invoiceId,
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

    public function getStamps(): array
    {
        return [];
    }
}
