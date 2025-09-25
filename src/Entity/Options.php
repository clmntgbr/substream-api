<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\UuidTrait;
use App\Repository\OptionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OptionsRepository::class)]
#[ApiResource(
    order: ['updatedAt' => 'DESC'],
    operations: [
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['option:read']],
        ),
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['option:read']],
        ),
    ]
)]
class Options
{
    use UuidTrait;
    use TimestampableEntity;
    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['option:read'])]
    private string $subtitleFont;
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Range(min: 8, max: 72)]
    #[Groups(['option:read'])]
    private int $subtitleSize;
    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read'])]
    private string $subtitleColor;
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read'])]
    private bool $subtitleBold;
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read'])]
    private bool $subtitleItalic;
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read'])]
    private bool $subtitleUnderline;
    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read'])]
    private string $subtitleOutlineColor;
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 10)]
    #[Groups(['option:read'])]
    private int $subtitleOutlineThickness;
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 5)]
    #[Groups(['option:read'])]
    private int $subtitleShadow;
    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read'])]
    private string $subtitleShadowColor;
    #[ORM\Column(type: Types::STRING, length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    #[Groups(['option:read'])]
    private string $videoFormat;
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['option:read'])]
    private int $videoParts;
    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Groups(['option:read'])]
    private float $yAxisAlignment;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getSubtitleFont(): ?string
    {
        return $this->subtitleFont;
    }

    public function getSubtitleSize(): ?int
    {
        return $this->subtitleSize;
    }

    public function getSubtitleColor(): ?string
    {
        return $this->subtitleColor;
    }

    public function isSubtitleBold(): ?bool
    {
        return $this->subtitleBold;
    }

    public function isSubtitleItalic(): ?bool
    {
        return $this->subtitleItalic;
    }

    public function isSubtitleUnderline(): ?bool
    {
        return $this->subtitleUnderline;
    }

    public function getSubtitleOutlineColor(): ?string
    {
        return $this->subtitleOutlineColor;
    }

    public function getSubtitleOutlineThickness(): ?int
    {
        return $this->subtitleOutlineThickness;
    }

    public function getSubtitleShadow(): ?int
    {
        return $this->subtitleShadow;
    }

    public function getSubtitleShadowColor(): ?string
    {
        return $this->subtitleShadowColor;
    }

    public function getVideoFormat(): ?string
    {
        return $this->videoFormat;
    }

    public function getVideoParts(): ?int
    {
        return $this->videoParts;
    }

    public function getYAxisAlignment(): ?float
    {
        return $this->yAxisAlignment;
    }

    public function setSubtitleFont(string $subtitleFont): static
    {
        $this->subtitleFont = $subtitleFont;
        return $this;
    }

    public function setSubtitleSize(int $subtitleSize): static
    {
        $this->subtitleSize = $subtitleSize;
        return $this;
    }

    public function setSubtitleColor(string $subtitleColor): static
    {
        $this->subtitleColor = $subtitleColor;
        return $this;
    }

    public function setSubtitleBold(bool $subtitleBold): static
    {
        $this->subtitleBold = $subtitleBold;
        return $this;
    }

    public function setSubtitleItalic(bool $subtitleItalic): static
    {
        $this->subtitleItalic = $subtitleItalic;
        return $this;
    }

    public function setSubtitleUnderline(bool $subtitleUnderline): static
    {
        $this->subtitleUnderline = $subtitleUnderline;
        return $this;
    }

    public function setSubtitleOutlineColor(string $subtitleOutlineColor): static
    {
        $this->subtitleOutlineColor = $subtitleOutlineColor;
        return $this;
    }

    public function setSubtitleOutlineThickness(int $subtitleOutlineThickness): static
    {
        $this->subtitleOutlineThickness = $subtitleOutlineThickness;
        return $this;
    }

    public function setSubtitleShadow(int $subtitleShadow): static
    {
        $this->subtitleShadow = $subtitleShadow;
        return $this;
    }

    public function setSubtitleShadowColor(string $subtitleShadowColor): static
    {
        $this->subtitleShadowColor = $subtitleShadowColor;
        return $this;
    }

    public function setVideoFormat(string $videoFormat): static
    {
        $this->videoFormat = $videoFormat;
        return $this;
    }

    public function setVideoParts(int $videoParts): static
    {
        $this->videoParts = $videoParts;
        return $this;
    }

    public function setYAxisAlignment(float $yAxisAlignment): static
    {
        $this->yAxisAlignment = $yAxisAlignment;
        return $this;
    }
}
