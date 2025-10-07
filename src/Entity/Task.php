<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Trait\UuidTrait;
use App\Enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ApiResource]
class Task
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\ManyToOne(targetEntity: Stream::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Stream $stream;

    #[ORM\Column(type: Types::STRING)]
    private string $commandClass;

    #[ORM\Column(type: Types::STRING)]
    private string $status;

    #[ORM\Column(type: Types::INTEGER)]
    private int $processingTime;

    public static function create(string $commandClass, Stream $stream): self
    {
        $task = new self();
        $task->setId(Uuid::v4());
        $task->commandClass = $commandClass;
        $task->status = TaskStatusEnum::RUNNING->value;
        $task->processingTime = 0;
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

    public function getProcessingTime(): int
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
}
