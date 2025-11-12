<?php

namespace App\Core\Domain\Payment\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Core\Domain\Payment\Repository\PaymentRepository;
use App\Core\Domain\Subscription\Entity\Subscription;
use App\Core\Domain\Trait\UuidTrait;
use App\Enum\PaymentProviderEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['payment:read']],
        ),
    ],
)]
class Payment
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $provider = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $customerId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $invoiceId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $paymentStatus = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $currency = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private ?string $amount = null;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Subscription $subscription;

    public function __construct()
    {
        $this->provider = PaymentProviderEnum::STRIPE->value;
        $this->id = Uuid::v7();
    }

    public static function create(
        Subscription $subscription,
        string $customerId,
        string $invoiceId,
        string $paymentStatus,
        string $currency,
        string $amount,
    ): self {
        $payment = new self();
        $payment->subscription = $subscription;
        $payment->customerId = $customerId;
        $payment->invoiceId = $invoiceId;
        $payment->paymentStatus = $paymentStatus;
        $payment->currency = $currency;
        $payment->amount = $amount;

        return $payment;
    }

    public function getProvider(): string
    {
        return $this->provider;
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
