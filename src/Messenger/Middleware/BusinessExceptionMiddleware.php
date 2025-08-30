<?php

namespace App\Messenger\Middleware;

use App\Exception\BusinessException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class BusinessExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (HandlerFailedException $e) {
            $originalException = $e->getPrevious();

            if ($originalException instanceof BusinessException) {
                throw $originalException;
            }

            throw $e;
        }
    }
}
