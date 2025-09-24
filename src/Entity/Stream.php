<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\UuidTrait;
use App\Repository\StreamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: StreamRepository::class)]
#[ApiResource(
    order: ['updatedAt' => 'DESC'],
    operations: [
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['stream:read', 'option:read']],
        ),
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['stream:read', 'option:read']],
        ),
    ]
)]
class Stream
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $fileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $fileNameTransformed = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $originalFileName = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['stream:read'])]
    private ?array $fileNamesGenerated = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['stream:read'])]
    private ?int $size = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['stream:read'])]
    private array $statuses = [];

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['stream:read'])]
    private array $audioFiles = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $subtitleSrtFile = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $subtitleAssFile = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['stream:read'])]
    private array $subtitleSrtFiles = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Options::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stream:read', 'option:read'])]
    private Options $options;

    public function __construct()
    {
        $this->options = new Options();
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getFileNameTransformed(): ?string
    {
        return $this->fileNameTransformed;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function getFileNamesGenerated(): ?array
    {
        return $this->fileNamesGenerated;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function getAudioFiles(): ?array
    {
        return $this->audioFiles;
    }

    public function getSubtitleSrtFile(): ?string
    {
        return $this->subtitleSrtFile;
    }

    public function getSubtitleAssFile(): ?string
    {
        return $this->subtitleAssFile;
    }

    public function getSubtitleSrtFiles(): ?array
    {
        return $this->subtitleSrtFiles;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getOptions(): ?Options
    {
        return $this->options;
    }
}
