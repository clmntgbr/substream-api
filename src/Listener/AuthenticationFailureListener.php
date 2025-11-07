<?php

declare(strict_types=1);

namespace App\Listener;

use App\Enum\TranslatableKeyEnum;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;

final class AuthenticationFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            Events::JWT_INVALID => 'onJWTInvalid',
            Events::JWT_NOT_FOUND => 'onJWTNotFound',
            Events::JWT_EXPIRED => 'onJWTExpired',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();

        $message = 'Authentication failed. Please verify your credentials and try again.';
        $key = TranslatableKeyEnum::AUTH_INVALID_CREDENTIALS->value;
        $params = [];

        if ($exception instanceof LockedException) {
            $message = 'Your account is locked. Please contact support to regain access.';
            $key = TranslatableKeyEnum::AUTH_ACCOUNT_LOCKED->value;
        } elseif ($exception instanceof DisabledException) {
            $message = 'Your account is disabled. Please confirm your email or contact support.';
            $key = TranslatableKeyEnum::AUTH_ACCOUNT_DISABLED->value;
        } elseif ($exception instanceof AuthenticationException) {
            $params = [
                'error' => $exception->getMessageKey(),
            ];
        }

        $event->setResponse($this->createResponse($message, $key, $params, Response::HTTP_UNAUTHORIZED));
    }

    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $event->setResponse($this->createResponse(
            'Your authentication token is invalid. Please log in again.',
            TranslatableKeyEnum::AUTH_TOKEN_INVALID->value
        ));
    }

    public function onJWTNotFound(JWTNotFoundEvent $event): void
    {
        $event->setResponse($this->createResponse(
            'No authentication token was provided. Please sign in to continue.',
            TranslatableKeyEnum::AUTH_TOKEN_MISSING->value
        ));
    }

    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $event->setResponse($this->createResponse(
            'Your authentication token has expired. Please refresh your session.',
            TranslatableKeyEnum::AUTH_TOKEN_EXPIRED->value
        ));
    }

    private function createResponse(string $message, string $key, array $params = [], int $statusCode = Response::HTTP_UNAUTHORIZED): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => $message,
            'key' => $key,
            'params' => $params,
        ], $statusCode);
    }
}
