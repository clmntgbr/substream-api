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
    private ?string $originalFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['stream:read'])]
    private array $statuses = [];

    public function __construct(string $id, ?string $fileName, ?string $originalFileName, ?string $url)
    {
        $this->id = Uuid::fromString($id);
        $this->fileName = $fileName;
        $this->originalFileName = $originalFileName;
        $this->url = $url;
        $this->status = StreamStatusEnum::PENDING->value;
        $this->statuses = [StreamStatusEnum::PENDING->value];
    }

    public static function create(string $id, ?string $fileName, ?string $originalFileName, ?string $url): self
    {
        return new self($id, $fileName, $originalFileName, $url);
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function setOriginalFileName(?string $originalFileName): static
    {
        $this->originalFileName = $originalFileName;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function setStatuses(array $statuses): static
    {
        $this->statuses = $statuses;

        return $this;
    }
}
