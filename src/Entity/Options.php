<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\UploadVideoOptions;
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

    #[ORM\Column(type: Types::STRING, length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
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

    public static function create(UploadVideoOptions $option): self
    {
        $entity = new self();
        $entity->setSubtitleFont($option->subtitleFont);
        $entity->setSubtitleSize($option->subtitleSize);
        $entity->setSubtitleColor($option->subtitleColor);
        $entity->setSubtitleBold($option->subtitleBold);
        $entity->setSubtitleItalic($option->subtitleItalic);
        $entity->setSubtitleUnderline($option->subtitleUnderline);
        $entity->setSubtitleOutlineColor($option->subtitleOutlineColor);
        $entity->setSubtitleOutlineThickness($option->subtitleOutlineThickness);
        $entity->setSubtitleShadow($option->subtitleShadow);
        $entity->setSubtitleShadowColor($option->subtitleShadowColor);
        $entity->setVideoFormat($option->videoFormat);
        $entity->setVideoParts($option->videoParts);
        $entity->setYAxisAlignment($option->yAxisAlignment);

        return $entity;
    }

    public function setSubtitleFont(string $subtitleFont): self
    {
        $this->subtitleFont = $subtitleFont;

        return $this;
    }

    public function setSubtitleSize(int $subtitleSize): self
    {
        $this->subtitleSize = $subtitleSize;

        return $this;
    }

    public function setSubtitleColor(string $subtitleColor): self
    {
        $this->subtitleColor = $subtitleColor;

        return $this;
    }

    public function setSubtitleBold(bool $subtitleBold): self
    {
        $this->subtitleBold = $subtitleBold;

        return $this;
    }

    public function setSubtitleItalic(bool $subtitleItalic): self
    {
        $this->subtitleItalic = $subtitleItalic;

        return $this;
    }

    public function setSubtitleUnderline(bool $subtitleUnderline): self
    {
        $this->subtitleUnderline = $subtitleUnderline;

        return $this;
    }

    public function setSubtitleOutlineColor(string $subtitleOutlineColor): self
    {
        $this->subtitleOutlineColor = $subtitleOutlineColor;

        return $this;
    }

    public function setSubtitleOutlineThickness(int $subtitleOutlineThickness): self
    {
        $this->subtitleOutlineThickness = $subtitleOutlineThickness;

        return $this;
    }

    public function setSubtitleShadow(int $subtitleShadow): self
    {
        $this->subtitleShadow = $subtitleShadow;

        return $this;
    }

    public function setSubtitleShadowColor(string $subtitleShadowColor): self
    {
        $this->subtitleShadowColor = $subtitleShadowColor;

        return $this;
    }

    public function setVideoFormat(string $videoFormat): self
    {
        $this->videoFormat = $videoFormat;

        return $this;
    }

    public function setVideoParts(int $videoParts): self
    {
        $this->videoParts = $videoParts;

        return $this;
    }

    public function setYAxisAlignment(float $yAxisAlignment): self
    {
        $this->yAxisAlignment = $yAxisAlignment;

        return $this;
    }

    public function getSubtitleFont(): string
    {
        return $this->subtitleFont;
    }

    public function getSubtitleSize(): int
    {
        return $this->subtitleSize;
    }

    public function getSubtitleColor(): string
    {
        return $this->subtitleColor;
    }

    public function getSubtitleBold(): bool
    {
        return $this->subtitleBold;
    }

    public function getSubtitleItalic(): bool
    {
        return $this->subtitleItalic;
    }

    public function getSubtitleUnderline(): bool
    {
        return $this->subtitleUnderline;
    }

    public function getSubtitleOutlineColor(): string
    {
        return $this->subtitleOutlineColor;
    }

    public function getSubtitleOutlineThickness(): int
    {
        return $this->subtitleOutlineThickness;
    }

    public function getSubtitleShadow(): int
    {
        return $this->subtitleShadow;
    }

    public function getSubtitleShadowColor(): string
    {
        return $this->subtitleShadowColor;
    }

    public function getVideoFormat(): string
    {
        return $this->videoFormat;
    }

    public function getVideoParts(): int
    {
        return $this->videoParts;
    }

    public function getYAxisAlignment(): float
    {
        return $this->yAxisAlignment;
    }
}
