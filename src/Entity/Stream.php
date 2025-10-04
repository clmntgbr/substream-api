<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Stream\CreateStreamUrlController;
use App\Controller\Stream\CreateStreamVideoController;
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
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
        ),
        new Post(
            uriTemplate: '/streams/video',
            controller: CreateStreamVideoController::class,
        ),
        new Post(
            uriTemplate: '/streams/url',
            controller: CreateStreamUrlController::class,
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
    private ?string $originalFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['stream:read'])]
    private ?int $size = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['stream:read'])]
    private array $audioFiles = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $subtitleSrtFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $subtitleAssFileName = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['stream:read'])]
    private array $statuses = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public static function create(
        Uuid $id,
        User $user,
        ?string $fileName = null,
        ?string $originalFileName = null,
        ?string $url = null,
        ?string $mimeType = null,
        ?int $size = null,
    ): self {
        $stream = new self();
        $stream->id = $id;
        $stream->fileName = $fileName;
        $stream->originalFileName = $originalFileName;
        $stream->url = $url;
        $stream->mimeType = $mimeType;
        $stream->size = $size;
        $stream->status = StreamStatusEnum::CREATED->value;
        $stream->statuses = [StreamStatusEnum::CREATED->value];
        $stream->user = $user;

        return $stream;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setStatus(string $status): self
    {
        $this->status = StreamStatusEnum::from($status)->value;
        $this->statuses[] = StreamStatusEnum::from($status)->value;

        return $this;
    }

    public function setStatuses(array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function markAsUploadFailed(): self
    {
        $this->status = StreamStatusEnum::UPLOAD_FAILED->value;
        $this->statuses[] = StreamStatusEnum::UPLOAD_FAILED->value;

        return $this;
    }

    public function markAsExtractSoundFailed(): self
    {
        $this->status = StreamStatusEnum::EXTRACTING_SOUND_FAILED->value;
        $this->statuses[] = StreamStatusEnum::EXTRACTING_SOUND_FAILED->value;

        return $this;
    }

    public function markAsUploaded(): self
    {
        $this->status = StreamStatusEnum::UPLOADED->value;
        $this->statuses[] = StreamStatusEnum::UPLOADED->value;

        return $this;
    }

    public function markAsUploading(): self
    {
        $this->status = StreamStatusEnum::UPLOADING->value;
        $this->statuses[] = StreamStatusEnum::UPLOADING->value;

        return $this;
    }

    public function markAsExtractSoundCompleted(): self
    {
        $this->status = StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value;
        $this->statuses[] = StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value;

        return $this;
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

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

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setAudioFiles(array $audioFiles): self
    {
        $this->audioFiles = $audioFiles;

        return $this;
    }

    public function setSubtitleSrtFileName(string $subtitleSrtFileName): self
    {
        $this->subtitleSrtFileName = $subtitleSrtFileName;

        return $this;
    }

    public function getSubtitleSrtFileName(): ?string
    {
        return $this->subtitleSrtFileName;
    }

    public function setSubtitleAssFileName(?string $subtitleAssFileName): self
    {
        $this->subtitleAssFileName = $subtitleAssFileName;

        return $this;
    }

    public function getSubtitleAssFileName(): ?string
    {
        return $this->subtitleAssFileName;
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
}
