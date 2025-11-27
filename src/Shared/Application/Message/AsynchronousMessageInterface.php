<?php

declare(strict_types=1);

namespace App\Shared\Application\Message;

use JsonSerializable;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

interface AsynchronousMessageInterface extends JsonSerializable
{
    /**
     * @return array<int, AmqpStamp>
     */
    public function getStamps(): array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array;

    public function getWebhookUrlSuccess(): string;

    public function getWebhookUrlFailure(): string;
}
