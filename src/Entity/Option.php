<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\UuidTrait;
use App\Enum\LanguageEnum;
use App\Enum\SubtitleFontEnum;
use App\Enum\VideoFormatEnum;
use App\Repository\OptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            normalizationContext: ['groups' => ['option:read:post']],
            denormalizationContext: ['groups' => ['option:write']],
            inputFormats: ['json' => ['application/json']],
        ),
    ],
)]
class Option
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Groups(['option:read', 'option:write'])]
    private string $subtitleFont;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 100)]
    #[Groups(['option:read', 'option:write'])]
    private int $subtitleSize;

    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read', 'option:write'])]
    private string $subtitleColor;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read', 'option:write'])]
    private bool $subtitleBold;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read', 'option:write'])]
    private bool $subtitleItalic;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Groups(['option:read', 'option:write'])]
    private bool $subtitleUnderline;

    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read', 'option:write'])]
    private string $subtitleOutlineColor;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 4)]
    #[Groups(['option:read', 'option:write'])]
    private int $subtitleOutlineThickness;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 4)]
    #[Groups(['option:read', 'option:write'])]
    private int $subtitleShadow;

    #[ORM\Column(type: Types::STRING, length: 7)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^#[0-9A-Fa-f]{6}$/')]
    #[Groups(['option:read', 'option:write'])]
    private string $subtitleShadowColor;

    #[ORM\Column(type: Types::STRING, length: 30)]
    #[Assert\NotBlank]
    #[Groups(['option:read', 'option:write'])]
    private string $format;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type(type: 'int')]
    #[Assert\Range(min: 1, max: 100)]
    #[Groups(['option:read', 'option:write'])]
    private int $chunkNumber;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 200)]
    #[Groups(['option:read', 'option:write'])]
    private float $yAxisAlignment;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    #[Groups(['option:read', 'option:write'])]
    private bool $isResume;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Groups(['option:read', 'option:write'])]
    private string $language;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->subtitleFont = SubtitleFontEnum::ARIAL->value;
        $this->subtitleSize = 16;
        $this->subtitleColor = '#000000';
        $this->subtitleBold = false;
        $this->subtitleItalic = false;
        $this->subtitleUnderline = false;
        $this->subtitleOutlineColor = '#000000';
        $this->subtitleOutlineThickness = 0;
        $this->subtitleShadow = 0;
        $this->subtitleShadowColor = '#000000';
        $this->format = VideoFormatEnum::ORIGINAL->value;
        $this->chunkNumber = 1;
        $this->yAxisAlignment = 0;
        $this->isResume = false;
        $this->language = LanguageEnum::AUTO->value;
    }

    #[Groups(['option:read:post', 'option:read'])]
    public function getId(): Uuid
    {
        return $this->id;
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

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getChunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function getYAxisAlignment(): float
    {
        return $this->yAxisAlignment;
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

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function setChunkNumber(int $chunkNumber): self
    {
        $this->chunkNumber = $chunkNumber;

        return $this;
    }

    public function setYAxisAlignment(float $yAxisAlignment): self
    {
        $this->yAxisAlignment = $yAxisAlignment;

        return $this;
    }

    public function setIsResume(bool $isResume): self
    {
        $this->isResume = $isResume;

        return $this;
    }

    public function getIsResume(): bool
    {
        return $this->isResume;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
