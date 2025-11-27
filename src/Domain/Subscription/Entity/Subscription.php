<?php

declare(strict_types=1);

namespace App\Domain\Subscription\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Domain\Payment\Entity\Payment;
use App\Domain\Plan\Entity\Plan;
use App\Domain\Subscription\Enum\SubscriptionStatusEnum;
use App\Domain\Subscription\Repository\SubscriptionRepository;
use App\Domain\Trait\UuidTrait;
use App\Domain\User\Entity\User;
use App\Presentation\Controller\Subscription\CreateSubscriptionController;
use App\Presentation\Controller\Subscription\GetBillingPortalUrlController;
use App\Presentation\Controller\Subscription\GetSubscriptionController;
use App\Presentation\Controller\Subscription\UpdateSubscriptionController;
use App\Presentation\Controller\Subscription\UpdateSubscriptionPreviewController;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Safe\DateTime;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new Post(
            uriTemplate: '/subscription/create',
            controller: CreateSubscriptionController::class,
        ),
        new Get(
            uriTemplate: '/subscription/manage',
            controller: GetBillingPortalUrlController::class,
        ),
        new Get(
            uriTemplate: '/subscription',
            controller: GetSubscriptionController::class,
        ),
        new Post(
            uriTemplate: '/subscription/update/preview',
            controller: UpdateSubscriptionPreviewController::class,
        ),
        new Post(
            uriTemplate: '/subscription/update',
            controller: UpdateSubscriptionController::class,
        ),
    ]
)]
class Subscription
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\ManyToOne(inversedBy: 'subscriptions', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Plan $plan;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['subscription:read'])]
    private DateTimeInterface $startDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $subscriptionId = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $checkoutSessionId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['subscription:read'])]
    private string $status;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isActive = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $autoRenew = true;

    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'subscription')]
    private Collection $payments;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->startDate = new DateTime();
        $this->payments = new ArrayCollection();
    }

    public static function create(
        User $user,
        Plan $plan,
        DateTimeInterface $startDate,
        string $status,
        bool $autoRenew = true,
        ?string $subscriptionId = null,
        ?string $checkoutSessionId = null,
    ): self {
        $subscription = new self();
        $subscription->user = $user;
        $subscription->isActive = true;
        $subscription->plan = $plan;
        $subscription->startDate = $startDate;
        $subscription->status = $status;
        $subscription->autoRenew = $autoRenew;
        $subscription->subscriptionId = $subscriptionId;
        $subscription->checkoutSessionId = $checkoutSessionId;

        return $subscription;
    }

    #[Groups(['subscription:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function setPlan(Plan $plan): self
    {
        $this->plan = $plan;
        $this->endDate = (new DateTime())->modify($plan->getExpirationDays());

        return $this;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isAutoRenew')]
    public function isAutoRenew(): bool
    {
        return $this->autoRenew;
    }

    public function setAutoRenew(bool $autoRenew): self
    {
        $this->autoRenew = $autoRenew;

        return $this;
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isPaidSubscription')]
    public function isPaidSubscription(): bool
    {
        return $this->plan->isPaid();
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isFreeSubscription')]
    public function isFreeSubscription(): bool
    {
        return $this->plan->isFree();
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isPendingCancel')]
    public function isPendingCancel(): bool
    {
        return $this->status === SubscriptionStatusEnum::PENDING_CANCEL->value;
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isExpired')]
    public function isExpired(): bool
    {
        if (null === $this->endDate) {
            return false;
        }

        return $this->endDate < new DateTime() && $this->status === SubscriptionStatusEnum::EXPIRED->value;
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isCanceled')]
    public function isCanceled(): bool
    {
        return $this->status === SubscriptionStatusEnum::CANCELED->value;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(string $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        $this->payments->add($payment);

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        $this->payments->removeElement($payment);

        return $this;
    }

    #[Groups(['subscription:read'])]
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['subscription:read'])]
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getCheckoutSessionId(): string
    {
        return $this->checkoutSessionId;
    }

    public function setCheckoutSessionId(string $checkoutSessionId): self
    {
        $this->checkoutSessionId = $checkoutSessionId;

        return $this;
    }

    public function expire(): self
    {
        $this->isActive = false;
        $this->status = SubscriptionStatusEnum::EXPIRED->value;
        $this->endDate = new DateTime();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
