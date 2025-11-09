<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\Subscription\GetSubscriptionController;
use App\Controller\Subscription\SubscribeController;
use App\Entity\Trait\UuidTrait;
use App\Enum\SubscriptionStatusEnum;
use App\Repository\SubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['subscription:read', 'plan:read']],
        ),
        new Get(),
        new Get(
            uriTemplate: '/subscribe/{planId}',
            controller: SubscribeController::class,
        ),
        new Get(
            uriTemplate: '/subscription',
            controller: GetSubscriptionController::class,
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
    #[Groups(['subscription:read'])]
    private Plan $plan;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['subscription:read'])]
    private \DateTimeInterface $endDate;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?string $subscriptionId = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['subscription:read'])]
    private string $status;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $autoRenew = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $canceledAt = null;

    #[ORM\OneToMany(targetEntity: StripePayment::class, mappedBy: 'subscription')]
    private Collection $stripePayments;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->startDate = new \DateTime();
        $this->stripePayments = new ArrayCollection();
    }

    public static function create(
        User $user,
        Plan $plan,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate,
        string $status,
        bool $autoRenew = true,
        ?string $subscriptionId = null,
    ): self {
        $subscription = new self();
        $subscription->user = $user;
        $subscription->plan = $plan;
        $subscription->startDate = $startDate;
        $subscription->endDate = $endDate;
        $subscription->status = $status;
        $subscription->autoRenew = $autoRenew;
        $subscription->subscriptionId = $subscriptionId;

        return $subscription;
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
        $this->endDate = (new \DateTime())->modify($plan->getExpirationDays());

        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
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
    #[SerializedName('isActive')]
    public function isActive(): bool
    {
        return $this->status === SubscriptionStatusEnum::ACTIVE->value;
    }

    #[Groups(['subscription:read'])]
    #[SerializedName('isExpired')]
    public function isExpired(): bool
    {
        if (null === $this->endDate) {
            return false;
        }

        return $this->endDate < new \DateTime() && $this->status === SubscriptionStatusEnum::EXPIRED->value;
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

    public function getStripePayments(): Collection
    {
        return $this->stripePayments;
    }

    public function addStripePayment(StripePayment $stripePayment): self
    {
        $this->stripePayments->add($stripePayment);

        return $this;
    }

    public function removeStripePayment(StripePayment $stripePayment): self
    {
        $this->stripePayments->removeElement($stripePayment);

        return $this;
    }

    #[Groups(['subscription:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['subscription:read'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
