<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Enum\User\Role;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;

class UserTest extends UnitTestCase
{
    public function testItInitializesWithUserRole(): void
    {
        // Arrange
        $user = new User('user@example.com');

        // Act
        $roles = $user->getRoles();

        // Assert
        $this->assertSame([Role::USER->value], $roles);
    }

    public function testItNormalizesAndValidatesRoles(): void
    {
        // Arrange
        $user = new User('user@example.com');

        // Act
        $user->setRoles(['role_admin', ' ROLE_USER  ']);

        // Assert
        $this->assertSame([Role::ADMIN->value, Role::USER->value], $user->getRoles());
    }

    public function testItRejectsInvalidRoles(): void
    {
        // Arrange
        $user = new User('user@example.com');

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('One or more provided roles are invalid.');

        // Act
        $user->setRoles(['role_invalid']);
    }
}
