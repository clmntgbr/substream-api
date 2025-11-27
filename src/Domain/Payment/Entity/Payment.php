<?php

namespace App\Domain\Payment\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Domain\Payment\Enum\PaymentProviderEnum;
use App\Domain\Payment\Repository\PaymentRepository;
use App\Domain\Subscription\Entity\Subscription;
use App\Domain\Trait\UuidTrait;
use App\Presentation\Controller\Payment\GetPaymentsInformationsController;
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
            uriTemplate: '/payments/informations',
            controller: GetPaymentsInformationsController::class,
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['payment:read']],
        ),
    ],
)]
class Payment
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $provider;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $customerId;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private string $invoiceId;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private string $invoiceUrl;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private string $paymentStatus;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['payment:read'])]
    private string $currency;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['payment:read'])]
    private int $amount;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Subscription $subscription;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public static function createFromStripe(
        Subscription $subscription,
        string $customerId,
        string $invoiceId,
        string $invoiceUrl,
        string $paymentStatus,
        string $currency,
        int $amount,
    ): self {
        $payment = new self();
        $payment->subscription = $subscription;
        $payment->customerId = $customerId;
        $payment->invoiceId = $invoiceId;
        $payment->invoiceUrl = $invoiceUrl;
        $payment->paymentStatus = $paymentStatus;
        $payment->currency = $currency;
        $payment->amount = $amount;
        $payment->provider = PaymentProviderEnum::STRIPE->value;

        return $payment;
    }

    #[Groups(['payment:read'])]
    public function getId(): Uuid
    {
        return $this->id;
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

    public function getAmount(): int
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

    public function getInvoiceUrl(): string
    {
        return $this->invoiceUrl;
    }

    #[Groups(['payment:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
