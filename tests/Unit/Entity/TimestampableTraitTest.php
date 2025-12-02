<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Trait\TimestampableTrait;
use App\Tests\Unit\UnitTestCase;
use DateTime;
use DateTimeImmutable;

class TimestampableTraitTest extends UnitTestCase
{
    public function testSetCreatedAtUsesUtcImmutable(): void
    {
        // Arrange
        $entity = $this->createEntity();

        // Act
        $entity->setCreatedAt();

        // Assert
        $createdAt = $entity->getCreatedAt();
        $this->assertInstanceOf(DateTimeImmutable::class, $createdAt);
        $this->assertSame('UTC', $createdAt->getTimezone()->getName());
    }

    public function testSetUpdatedAtUsesUtcMutable(): void
    {
        // Arrange
        $entity = $this->createEntity();

        // Act
        $entity->setUpdatedAt();

        // Assert
        $updatedAt = $entity->getUpdatedAt();
        $this->assertInstanceOf(DateTime::class, $updatedAt);
        $this->assertSame('UTC', $updatedAt->getTimezone()->getName());
    }

    public function testSoftDeleteMarksEntityAsDeleted(): void
    {
        // Arrange
        $entity = $this->createEntity();

        // Act
        $entity->softDelete();

        // Assert
        $this->assertTrue($entity->isDeleted());
        $this->assertInstanceOf(DateTimeImmutable::class, $entity->getDeletedAt());
    }

    private function createEntity(): object
    {
        return new class() {
            use TimestampableTrait;
        };
    }
}
