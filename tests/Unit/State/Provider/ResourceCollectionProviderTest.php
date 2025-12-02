<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\State\Provider\ResourceCollectionProvider;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

class ResourceCollectionProviderTest extends UnitTestCase
{
    public function testItMapsEntitiesToResources(): void
    {
        // Arrange
        $operation = $this->mock(Operation::class);
        $operation->method('getClass')->willReturn(DummyResource::class);
        $operation->method('getOutput')->willReturn(DummyResource::class);

        $entity = new \stdClass();
        $mapped = new DummyResource();

        $doctrineProvider = $this->mock(ProviderInterface::class);
        $doctrineProvider->method('provide')->willReturn([$entity]);

        $objectMapper = $this->mock(ObjectMapperInterface::class);
        $objectMapper->expects(self::once())
            ->method('map')
            ->with($entity, DummyResource::class)
            ->willReturn($mapped);

        $provider = new ResourceCollectionProvider($doctrineProvider, $objectMapper);

        // Act
        $resources = $provider->provide($operation);

        // Assert
        $this->assertSame([$mapped], $resources);
    }

    public function testItFailsWhenTargetClassIsMissing(): void
    {
        // Arrange
        $operation = $this->mock(Operation::class);
        $operation->method('getClass')->willReturn('App\\MissingClass');
        $operation->method('getOutput')->willReturn(null);

        $doctrineProvider = $this->mock(ProviderInterface::class);
        $doctrineProvider->method('provide')->willReturn([]);

        $objectMapper = $this->mock(ObjectMapperInterface::class);

        $provider = new ResourceCollectionProvider($doctrineProvider, $objectMapper);

        // Assert
        $this->expectException(\LogicException::class);

        // Act
        $provider->provide($operation);
    }
}

final class DummyResource
{
}
