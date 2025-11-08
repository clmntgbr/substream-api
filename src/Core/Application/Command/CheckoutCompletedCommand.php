<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;

final class CheckoutCompletedCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private string $checkoutSessionId,
        private string $userId,
        private string $userEmail,
        private string $planId,
        private string $stripeCustomerId,
        private string $stripeSubscriptionId,
        private string $stripeInvoiceId,
        private string $paymentStatus,
        private string $amount,
        private string $currency,
    ) {
    }

    public function getCheckoutSessionId(): string
    {
        return $this->checkoutSessionId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getPlanId(): string
    {
        return $this->planId;
    }

    public function getStripeCustomerId(): string
    {
        return $this->stripeCustomerId;
    }

    public function getStripeSubscriptionId(): string
    {
        return $this->stripeSubscriptionId;
    }

    public function getStripeInvoiceId(): string
    {
        return $this->stripeInvoiceId;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
