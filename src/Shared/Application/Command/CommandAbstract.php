<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

abstract class CommandAbstract
{
    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array
    {
        return [];
    }
}
