<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\UploadVideoOptions;
use App\Entity\Trait\UuidTrait;
use App\Enum\StreamStatusEnum;
use App\Repository\StreamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

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
    private ?string $originalName = null;

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
        $this->status = StreamStatusEnum::UPLOADING->value;
        $this->statuses = [StreamStatusEnum::UPLOADING->value];
    }

    public static function create(Uuid $uuid, User $user, UploadVideoOptions $options, ?string $url = null): self
    {
        $stream = new self();
        $stream->id = $uuid;
        $stream->user = $user;
        $stream->url = $url;
        $stream->options = Options::create($options);

        return $stream;
    }

    public function markAsExtractingSoundProcessing(): self
    {
        $this->setStatus(StreamStatusEnum::EXTRACTING_SOUND_PROCESSING->value);

        return $this;
    }

    public function markAsTransformingSubtitlesProcessing(): self
    {
        $this->setStatus(StreamStatusEnum::TRANSFORMING_SUBTITLES_PROCESSING->value);

        return $this;
    }

    public function markAsGeneratingSubtitlesProcessing(): self
    {
        $this->setStatus(StreamStatusEnum::GENERATING_SUBTITLES_PROCESSING->value);

        return $this;
    }

    public function updateStream(string $fileName, string $originalName, string $mimeType, int $size): self
    {
        $this->fileName = $fileName;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;

        return $this;
    }

    public function markAsUploaded(): self
    {
        $this->setStatus(StreamStatusEnum::UPLOADED->value);

        return $this;
    }

    public function markAsFailed(StreamStatusEnum $status): self
    {
        $this->setStatus($status->value);

        return $this;
    }

    public function markAsGeneratedSubtitles(string $subtitleSrtFile, array $subtitleSrtFiles): self
    {
        $this->subtitleSrtFile = $subtitleSrtFile;
        $this->subtitleSrtFiles = $subtitleSrtFiles;
        $this->setStatus(StreamStatusEnum::GENERATED_SUBTITLES->value);

        return $this;
    }

    public function markAsExtractedSound(array $audioFiles): self
    {
        $this->audioFiles = $audioFiles;
        $this->setStatus(StreamStatusEnum::EXTRACTED_SOUND->value);

        return $this;
    }

    #[Groups(['stream:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    #[Groups(['stream:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['stream:read'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->statuses[] = $status;

        return $this;
    }

    public function initStatuses(): self
    {
        $this->statuses = [];

        return $this;
    }

    public function setStatuses(array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function getSubtitleSrtFile(): ?string
    {
        return $this->subtitleSrtFile;
    }

    public function getSubtitleSrtFiles(): array
    {
        return $this->subtitleSrtFiles;
    }

    public function setSubtitleSrtFile(string $subtitleSrtFile): self
    {
        $this->subtitleSrtFile = $subtitleSrtFile;

        return $this;
    }

    public function setSubtitleSrtFiles(array $subtitleSrtFiles): self
    {
        $this->subtitleSrtFiles = $subtitleSrtFiles;

        return $this;
    }

    public function setAudioFiles(array $audioFiles): self
    {
        $this->audioFiles = $audioFiles;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setOptions(Options $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }
}
