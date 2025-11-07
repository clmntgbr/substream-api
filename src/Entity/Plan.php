<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\UuidTrait;
use App\Repository\PlanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['plan:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['plan:read']],
        ),
    ]
)]
class Plan
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['plan:read'])]
    private string $name;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['plan:read'])]
    private float $price;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['plan:read'])]
    private string $interval;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['plan:read'])]
    private int $maxVideosPerMonth;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['plan:read'])]
    private int $maxSizeInMegabytes;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['plan:read'])]
    private int $maxDurationMinutes;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['plan:read'])]
    private array $features;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['plan:read'])]
    private bool $isActive;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->isActive = true;
        $this->features = [];
    }

    #[Groups(['plan:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setInterval(string $interval): self
    {
        $this->interval = $interval;

        return $this;
    }

    public function setFeatures(array $features): self
    {
        $this->features = $features;

        return $this;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getMaxVideosPerMonth(): int
    {
        return $this->maxVideosPerMonth;
    }

    public function getMaxSizeInMegabytes(): int
    {
        return $this->maxSizeInMegabytes;
    }

    public function getMaxDurationMinutes(): int
    {
        return $this->maxDurationMinutes;
    }

    public function setMaxVideosPerMonth(int $maxVideosPerMonth): self
    {
        $this->maxVideosPerMonth = $maxVideosPerMonth;

        return $this;
    }

    public function setMaxSizeInMegabytes(int $maxSizeInMegabytes): self
    {
        $this->maxSizeInMegabytes = $maxSizeInMegabytes;

        return $this;
    }

    public function setMaxDurationMinutes(int $maxDurationMinutes): self
    {
        $this->maxDurationMinutes = $maxDurationMinutes;

        return $this;
    }
}
