<?php

declare(strict_types=1);

namespace App\Domain\Notification\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Domain\Notification\Repository\NotificationRepository;
use App\Domain\Trait\UuidTrait;
use App\Domain\User\Entity\User;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use RuntimeException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['notification:read']],
        ),
        new Patch(
            uriTemplate: '/notifications/{id}/read',
            normalizationContext: ['groups' => ['notification:read']],
            denormalizationContext: ['groups' => ['notification:write']],
        ),
    ],
)]
class Notification
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['notification:read'])]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read'])]
    private string $message;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read'])]
    private string $context;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read'])]
    private string $contextMessage;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['notification:read'])]
    private Uuid $contextId;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['notification:read', 'notification:write'])]
    private bool $isRead = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->isRead = false;
    }

    public static function create(
        string $title,
        string $message,
        string $context,
        Uuid $contextId,
        string $contextMessage,
        User $user,
    ): self {
        $notification = new self();
        $notification->title = $title;
        $notification->message = $message;
        $notification->context = $context;
        $notification->contextId = $contextId;
        $notification->contextMessage = $contextMessage;
        $notification->user = $user;

        return $notification;
    }

    #[Groups(['notification:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getContextId(): Uuid
    {
        return $this->contextId;
    }

    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setContext(string $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function setContextId(Uuid $contextId): self
    {
        $this->contextId = $contextId;

        return $this;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setContextMessage(string $contextMessage): self
    {
        $this->contextMessage = $contextMessage;

        return $this;
    }

    public function setContextIdFromString(string $uuid): self
    {
        $this->contextId = Uuid::fromString($uuid);

        return $this;
    }

    public function getContextMessage(): string
    {
        return $this->contextMessage;
    }

    #[Groups(['notification:search', 'elastica'])]
    public function getUserUuid(): string
    {
        return (string) $this->user->getId();
    }

    #[Groups(['notification:read'])]
    public function getCreatedAt(): DateTimeInterface
    {
        if (null === $this->createdAt) {
            throw new RuntimeException('CreatedAt is not set');
        }

        return $this->createdAt;
    }

    #[Groups(['notification:read'])]
    public function getUpdatedAt(): DateTimeInterface
    {
        if (null === $this->updatedAt) {
            throw new RuntimeException('UpdatedAt is not set');
        }

        return $this->updatedAt;
    }
}
