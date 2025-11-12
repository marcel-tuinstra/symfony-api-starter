<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException', priority: -80)]
final class ProblemExceptionListener
{
    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        $throwable = $exceptionEvent->getThrowable();
        $request = $exceptionEvent->getRequest();

        // Determine status code
        $statusCode = $throwable instanceof HttpExceptionInterface
            ? $throwable->getStatusCode()
            : 500;

        // Determine title & type
        $title = match ($statusCode) {
            400 => 'Validation failed',
            403 => 'Access denied',
            404 => 'Resource not found',
            default => 'Internal server error',
        };

        $problem = [
            'type' => sprintf('https://symfony-api-starter.dev/errors/%s', str_replace(' ', '_', strtolower($title))),
            'title' => $title,
            'status' => $statusCode,
            'detail' => $throwable->getMessage(),
            'instance' => $request->getPathInfo(),
        ];

        $jsonResponse = new JsonResponse(
            $problem,
            $statusCode,
            [
                'Content-Type' => 'application/problem+json',
            ]
        );

        $exceptionEvent->setResponse($jsonResponse);
    }
}
