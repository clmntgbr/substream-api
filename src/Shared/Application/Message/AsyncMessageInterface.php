<?php

declare(strict_types=1);

namespace App\Shared\Application\Message;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

interface AsyncMessageInterface
{
    public function getRoutingKey(): AmqpStamp;
}
