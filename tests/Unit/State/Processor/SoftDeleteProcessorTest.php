<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Contract\TimestampedResourceInterface;
use App\State\Processor\SoftDeleteProcessor;
use App\Tests\Unit\UnitTestCase;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

class SoftDeleteProcessorTest extends UnitTestCase
{
    public function testItSoftDeletesAndMapsResource(): void
    {
        // Arrange
        $entity = new class() implements TimestampedResourceInterface, \App\Entity\Interface\TimestampableInterface {
            public bool $deleted = false;

            public function softDelete(): void
            {
                $this->deleted = true;
            }

            public function isDeleted(): bool
            {
                return $this->deleted;
            }

            public function getCreatedAt(): \DateTimeImmutable
            {
                return new \DateTimeImmutable();
            }

            public function getUpdatedAt(): ?\DateTimeInterface
            {
                return null;
            }

            public function getDeletedAt(): ?\DateTimeImmutable
            {
                return null;
            }
        };

        $operation = $this->mock(Operation::class);
        $operation->method('getClass')->willReturn(DummyTimestampedResource::class);

        $persistProcessor = $this->mock(ProcessorInterface::class);
        $persistProcessor->expects(self::once())
            ->method('process')
            ->with($entity, $operation, [], [])
            ->willReturn($entity);

        $mappedResource = new DummyTimestampedResource();
        $objectMapper = $this->mock(ObjectMapperInterface::class);
        $objectMapper->expects(self::once())
            ->method('map')
            ->with($entity, DummyTimestampedResource::class)
            ->willReturn($mappedResource);

        $processor = new SoftDeleteProcessor($persistProcessor, $objectMapper);

        // Act
        $result = $processor->process($entity, $operation);

        // Assert
        $this->assertTrue($entity->deleted);
        $this->assertSame($mappedResource, $result);
    }
}

final class DummyTimestampedResource implements TimestampedResourceInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return null;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return null;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return null;
    }
}
