<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Notification\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class CreateNotificationCommand implements AsynchronousInterface
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

    public function getStamps(): array
    {
        return [];
    }
}
