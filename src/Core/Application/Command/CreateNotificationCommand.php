<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\CommandAbstract;
use Symfony\Component\Uid\Uuid;

final class CreateNotificationCommand extends CommandAbstract implements AsyncCommandInterface
{
    public function __construct(
        private string $title,
        private string $message,
        private string $context,
        private Uuid $contextId,
    ) {
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
}
