<?php

declare(strict_types=1);

namespace App\Domain\Task\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Task\Enum\TaskStatusEnum;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Trait\UuidTrait;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use RuntimeException;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

use function sprintf;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource]
class Task
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\ManyToOne(targetEntity: Stream::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Stream $stream;

    #[Groups(['stream:read'])]
    #[ORM\Column(type: Types::STRING)]
    private string $commandClass;

    #[Groups(['stream:read'])]
    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $processingTime = null;

    public static function create(string $commandClass, Stream $stream): self
    {
        $task = new self();
        $task->setId(Uuid::v7());
        $task->commandClass = $commandClass;
        $task->status = TaskStatusEnum::RUNNING->value;
        $task->stream = $stream;

        return $task;
    }

    public function getStream(): Stream
    {
        return $this->stream;
    }

    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    public function getStatus(): TaskStatusEnum
    {
        return TaskStatusEnum::from($this->status);
    }

    public function getProcessingTime(): ?int
    {
        return $this->processingTime;
    }

    public function setCommandClass(string $commandClass): self
    {
        $this->commandClass = $commandClass;

        return $this;
    }

    public function setStatus(TaskStatusEnum $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function markAsCompleted(): self
    {
        $this->status = TaskStatusEnum::COMPLETED->value;

        return $this;
    }

    public function markAsFailed(): self
    {
        $this->status = TaskStatusEnum::FAILED->value;

        return $this;
    }

    public function setProcessingTime(int $processingTime): self
    {
        $this->processingTime = $processingTime;

        return $this;
    }

    public function getProcessingTimeInSeconds(): int
    {
        return $this->processingTime / 1000;
    }

    #[Groups(['stream:read'])]
    public function getCreatedAt(): DateTimeInterface
    {
        if (null === $this->createdAt) {
            throw new RuntimeException('CreatedAt is not set');
        }

        return $this->createdAt;
    }

    #[Groups(['stream:read'])]
    public function getUpdatedAt(): DateTimeInterface
    {
        if (null === $this->updatedAt) {
            throw new RuntimeException('UpdatedAt is not set');
        }

        return $this->updatedAt;
    }

    #[Groups(['stream:read'])]
    #[SerializedName('processingTime')]
    public function getProcessingTimeFormatted(): ?string
    {
        if (null === $this->processingTime) {
            return null;
        }

        $totalSeconds = (int) floor($this->processingTime / 1000);
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
