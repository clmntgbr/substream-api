<?php

declare(strict_types=1);

namespace App\Listener;

use App\Enum\ErrorKeyEnum;
use App\Exception\BusinessException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class BusinessExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $env,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 20],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BusinessException) {
            $this->handleBusinessException($exception, $event);

            return;
        }

        if ($exception instanceof HttpException) {
            $this->handleHttpException($exception, $event);

            return;
        }

        $this->handleGenericException($exception, $event);
    }

    private function handleBusinessException(BusinessException $exception, ExceptionEvent $event): void
    {
        $this->logger->warning('Business exception occurred', [
            'message' => $exception->getEnglishMessage(),
            'translationKey' => $exception->getTranslationKey(),
            'translationParams' => $exception->getTranslationParams(),
            'statusCode' => $exception->getHttpStatusCode(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $response = new JsonResponse([
            'success' => false,
            'message' => $exception->getEnglishMessage(),
            'key' => $exception->getTranslationKey(),
            'params' => $exception->getTranslationParams(),
        ], $exception->getHttpStatusCode());

        $event->setResponse($response);
    }

    private function handleHttpException(HttpException $exception, ExceptionEvent $event): void
    {
        $this->logger->info('HTTP exception occurred', [
            'message' => $exception->getMessage(),
            'statusCode' => $exception->getStatusCode(),
        ]);

        $response = new JsonResponse([
            'success' => false,
            'message' => $exception->getMessage(),
            'key' => ErrorKeyEnum::httpStatus($exception->getStatusCode()),
            'params' => [],
        ], $exception->getStatusCode());

        $event->setResponse($response);
    }

    private function handleGenericException(\Throwable $exception, ExceptionEvent $event): void
    {
        $this->logger->error('Unexpected exception occurred', [
            'message' => $exception->getMessage(),
            'class' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $isProduction = 'prod' === $this->env;
        $errorKey = $isProduction ? ErrorKeyEnum::SERVER_INTERNAL->value : ErrorKeyEnum::SERVER_INTERNAL_DEBUG->value;
        $message = $isProduction
            ? 'An internal server error occurred'
            : sprintf('Internal error: %s in %s:%d', $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $params = $isProduction
            ? []
            : [
                'message' => $exception->getMessage(),
                'class' => $exception::class,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];

        $response = new JsonResponse([
            'success' => false,
            'message' => $message,
            'key' => $errorKey,
            'params' => $params,
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

        $event->setResponse($response);
    }
}
