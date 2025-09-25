<?php

declare(strict_types=1);

namespace App\CQRS\Middleware;

use App\CQRS\Stamp\SyncStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class SyncExecutionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Vérifier si le message a un SyncStamp
        $syncStamp = $envelope->last(SyncStamp::class);
        
        if ($syncStamp) {
            // Court-circuiter le transport et exécuter directement
            return $stack->next()->handle($envelope, $stack);
        }

        // Sinon, laisser passer au transport asynchrone
        return $stack->next()->handle($envelope, $stack);
    }
}
