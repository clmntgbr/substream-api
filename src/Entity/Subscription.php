<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\UuidTrait;
use App\Enum\SubscriptionStatusEnum;
use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ApiResource(
    operations: []
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
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['subscription:read'])]
    private string $status;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['subscription:read'])]
    private bool $autoRenew = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['subscription:read'])]
    private ?\DateTimeInterface $canceledAt = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public static function create(
        User $user,
        Plan $plan,
        \DateTimeInterface $startDate,
        string $status,
        bool $autoRenew = true,
    ): self {
        $subscription = new self();
        $subscription->user = $user;
        $subscription->plan = $plan;
        $subscription->startDate = $startDate;
        $subscription->status = $status;
        $subscription->autoRenew = $autoRenew;

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
}
