<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Repository\UserRepository;
use App\Tests\Unit\UnitTestCase;
use Doctrine\Persistence\ManagerRegistry;

class UserRepositoryTest extends UnitTestCase
{
    public function testRepositoryIsConstructedWithUserClass(): void
    {
        // Arrange
        $registry = $this->mock(ManagerRegistry::class);

        // Act
        $repository = new UserRepository($registry);

        // Assert
        $this->assertInstanceOf(UserRepository::class, $repository);
    }
}
