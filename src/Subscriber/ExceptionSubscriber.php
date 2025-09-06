<?php

namespace App\Subscriber;

use App\Exception\BusinessException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BusinessException) {
            $response = new JsonResponse([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], $exception->getCode());

            $event->setResponse($response);
        }
    }
}
