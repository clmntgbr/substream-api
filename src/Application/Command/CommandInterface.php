<?php

namespace App\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

interface CommandInterface
{
    public function getAmqpStamp(): ?AmqpStamp;
}
