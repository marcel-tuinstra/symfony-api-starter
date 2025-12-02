<?php

declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\EventListener\ProblemExceptionListener;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ProblemExceptionListenerTest extends UnitTestCase
{
    public function testItMapsExceptionToProblemJson(): void
    {
        // Arrange
        $listener = new ProblemExceptionListener();
        $kernel = $this->mock(HttpKernelInterface::class);
        $request = Request::create('/missing');
        $exception = new NotFoundHttpException('Nope');
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        // Act
        $listener->onKernelException($event);

        // Assert
        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('application/problem+json', $response->headers->get('Content-Type'));

        $data = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Resource not found', $data['title']);
        $this->assertSame('Nope', $data['detail']);
        $this->assertSame('/missing', $data['instance']);
        $this->assertSame(404, $data['status']);
    }
}
