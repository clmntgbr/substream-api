<?php

namespace App\Listener;

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

        // Check if it's a validation exception (422 status code)
        if (!$exception instanceof HttpException || Response::HTTP_UNPROCESSABLE_ENTITY !== $exception->getStatusCode()) {
            return;
        }

        // Try to get the previous exception which might contain the validation violations
        $previousException = $exception->getPrevious();
        $errors = [];

        // Check if the previous exception has validation violations
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
            // Fallback: Parse the concatenated error message
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
                return; // Not a validation error, let other handlers manage it
            }
        }

        // Create a structured error response
        $response = new JsonResponse([
            '@context' => '/api/contexts/Error',
            '@id' => '/api/errors/422',
            '@type' => 'ConstraintViolationList',
            'title' => 'An error occurred',
            'detail' => 'Validation failed',
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'type' => '/errors/422',
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
    }

    private function extractFieldName(string $message): string
    {
        // Common patterns for field names in error messages
        $patterns = [
            '/^([a-z][a-zA-Z0-9]*)\s*:/i',  // "fieldName: error message"
            '/^([a-z][a-zA-Z0-9]*)\s+/i',    // "fieldName error message"
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message, $matches)) {
                return lcfirst($matches[1]);
            }
        }

        // Check for specific error messages and map them to fields
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
