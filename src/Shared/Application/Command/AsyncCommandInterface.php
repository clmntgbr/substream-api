<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;

interface AsyncCommandInterface
{
    /**
     * @return AmqpStamp[]
     */
    public function getStamps(): array;
}
