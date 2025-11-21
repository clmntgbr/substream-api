<?php

declare(strict_types=1);

namespace App\Application\Payment\Command;

use App\Shared\Application\Command\AsynchronousPriorityInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateCheckoutCommand implements AsynchronousPriorityInterface
{
    public function __construct(
        #[Assert\NotBlank]
        private ?string $checkoutSessionId,
        #[Assert\NotBlank]
        private ?string $userId,
        #[Assert\NotBlank]
        private ?string $userEmail,
        #[Assert\NotBlank]
        private ?string $planId,
        #[Assert\NotBlank]
        private ?string $stripeCustomerId,
        #[Assert\NotBlank]
        private ?string $subscriptionId,
        #[Assert\NotBlank]
        private ?string $stripeInvoiceId,
        #[Assert\NotBlank]
        private ?string $paymentStatus,
        #[Assert\NotBlank]
        private ?string $amount,
        #[Assert\NotBlank]
        private ?string $currency,
    ) {
    }

    public function getCheckoutSessionId(): string
    {
        if (null === $this->checkoutSessionId) {
            throw new \Exception('Checkout session ID is required');
        }

        return $this->checkoutSessionId;
    }

    public function getUserId(): string
    {
        if (null === $this->userId) {
            throw new \Exception('User ID is required');
        }

        return $this->userId;
    }

    public function getUserEmail(): string
    {
        if (null === $this->userEmail) {
            throw new \Exception('User email is required');
        }

        return $this->userEmail;
    }

    public function getPlanId(): string
    {
        if (null === $this->planId) {
            throw new \Exception('Plan ID is required');
        }

        return $this->planId;
    }

    public function getStripeCustomerId(): string
    {
        if (null === $this->stripeCustomerId) {
            throw new \Exception('Stripe customer ID is required');
        }

        return $this->stripeCustomerId;
    }

    public function getSubscriptionId(): string
    {
        if (null === $this->subscriptionId) {
            throw new \Exception('Subscription ID is required');
        }

        return $this->subscriptionId;
    }

    public function getStripeInvoiceId(): string
    {
        if (null === $this->stripeInvoiceId) {
            throw new \Exception('Stripe invoice ID is required');
        }

        return $this->stripeInvoiceId;
    }

    public function getPaymentStatus(): string
    {
        if (null === $this->paymentStatus) {
            throw new \Exception('Payment status is required');
        }

        return $this->paymentStatus;
    }

    public function getAmount(): string
    {
        if (null === $this->amount) {
            throw new \Exception('Amount is required');
        }

        return $this->amount;
    }

    public function getCurrency(): string
    {
        if (null === $this->currency) {
            throw new \Exception('Currency is required');
        }

        return $this->currency;
    }

    public function getStamps(): array
    {
        return [];
    }
}
