<?php

declare(strict_types=1);

namespace App\CQRS\Middleware;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class BusinessExceptionMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (\Throwable $exception) {
            // Si c'est une exception métier, la laisser passer pour les requêtes
            // ou l'encapsuler dans UnrecoverableMessageHandlingException pour les commandes
            if ($this->isBusinessException($exception)) {
                $message = $envelope->getMessage();
                
                // Pour les commandes, encapsuler pour éviter les retries
                if (str_contains(get_class($message), 'Command')) {
                    throw new UnrecoverableMessageHandlingException(
                        'Business exception: ' . $exception->getMessage(),
                        0,
                        $exception
                    );
                }
                
                // Pour les requêtes, laisser passer l'exception
                throw $exception;
            }
            
            // Pour les autres exceptions, laisser passer
            throw $exception;
        }
    }

    private function isBusinessException(\Throwable $exception): bool
    {
        // Liste des exceptions métier connues
        $businessExceptions = [
            'UserNotFoundException',
            'InsufficientStockException',
            'ValidationException',
            'BusinessLogicException',
        ];

        $exceptionClass = (new \ReflectionClass($exception))->getShortName();
        
        return in_array($exceptionClass, $businessExceptions, true);
    }
}
