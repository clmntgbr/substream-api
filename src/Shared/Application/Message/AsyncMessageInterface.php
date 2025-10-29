<?php

declare(strict_types=1);

namespace App\Shared\Application\Message;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

interface AsyncMessageInterface extends \JsonSerializable
{
    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array;

    public function jsonSerialize(): array;

    public function getWebhookUrlSuccess(): string;

    public function getWebhookUrlFailure(): string;
}
