<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
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
    operations: [
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['stream:read']],
        ),
        new GetCollection(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['stream:read']],
        ),
    ]
)]
class Stream
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $fileName;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $originalName;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $mimeType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['stream:read'])]
    private int $size;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Groups(['clip.read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $statuses = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->status = StreamStatusEnum::UPLOADED->value;
        $this->statuses = [StreamStatusEnum::UPLOADED->value];
    }

    public function create(Uuid $uuid, string $fileName, string $originalName, string $mimeType, int $size, User $user): self
    {
        $this->id = $uuid;
        $this->fileName = $fileName;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->user = $user;

        return $this;
    }

    #[Groups(['stream:read'])]
    public function getId(): string
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
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
}
