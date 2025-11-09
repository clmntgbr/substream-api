<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\UuidTrait;
use App\Repository\StripePaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StripePaymentRepository::class)]
#[ApiResource]
class StripePayment
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $checkoutSessionId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $customerId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $invoiceId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $paymentStatus = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $currency = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $amount = null;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['subscription:read'])]
    private Subscription $subscription;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public static function create(
        Subscription $subscription,
        string $checkoutSessionId,
        string $customerId,
        string $invoiceId,
        string $paymentStatus,
        string $currency,
        string $amount,
    ): self {
        $stripePayment = new self();
        $stripePayment->subscription = $subscription;
        $stripePayment->checkoutSessionId = $checkoutSessionId;
        $stripePayment->customerId = $customerId;
        $stripePayment->invoiceId = $invoiceId;
        $stripePayment->paymentStatus = $paymentStatus;
        $stripePayment->currency = $currency;
        $stripePayment->amount = $amount;

        return $stripePayment;
    }

    public function getCheckoutSessionId(): string
    {
        return $this->checkoutSessionId;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
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

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }
}
