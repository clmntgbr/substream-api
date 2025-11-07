<?php

declare(strict_types=1);

namespace App\Listener;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ValidationExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof HttpException || Response::HTTP_UNPROCESSABLE_ENTITY !== $exception->getStatusCode()) {
            return;
        }

        $previousException = $exception->getPrevious();
        $errors = [];

        if ($previousException && method_exists($previousException, 'getViolations')) {
            $violations = $previousException->getViolations();

            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $message = $violation->getMessage();

                if (!isset($errors[$propertyPath])) {
                    $errors[$propertyPath] = [];
                }

                $errors[$propertyPath][] = $message;
            }
        } else {
            $message = $exception->getMessage();

            if (false !== strpos($message, "\n")) {
                $lines = explode("\n", $message);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) {
                        continue;
                    }

                    $fieldName = $this->extractFieldName($line);

                    if (!isset($errors[$fieldName])) {
                        $errors[$fieldName] = [];
                    }

                    $errors[$fieldName][] = $line;
                }
            } else {
                return;
            }
        }

        $response = new JsonResponse([
            'success' => false,
            'message' => 'Validation failed',
            'key' => ErrorKeyEnum::VALIDATION_FAILED->value,
            'params' => [
                'errors' => $errors,
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
    }

    private function extractFieldName(string $message): string
    {
        $patterns = [
            '/^([a-z][a-zA-Z0-9]*)\s*:/i',
            '/^([a-z][a-zA-Z0-9]*)\s+/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                return lcfirst($matches[1]);
            }
        }

        if (false !== stripos($message, 'email')) {
            return 'email';
        }
        if (false !== stripos($message, 'password') && false !== stripos($message, 'match')) {
            return 'confirmPassword';
        }
        if (false !== stripos($message, 'password')) {
            return 'plainPassword';
        }
        if (false !== stripos($message, 'first name') || false !== stripos($message, 'firstname')) {
            return 'firstname';
        }
        if (false !== stripos($message, 'last name') || false !== stripos($message, 'lastname')) {
            return 'lastname';
        }

        return 'general';
    }
}
