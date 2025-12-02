<?php

declare(strict_types=1);

namespace App\Tests\Integration\Repository;

use App\Enum\User\Role;
use App\Tests\Fixture\UserTestTrait;
use App\Tests\Integration\IntegrationTestCase;

class UserRepositoryTest extends IntegrationTestCase
{
    use UserTestTrait;

    public function testItFindsUserByUuid(): void
    {
        // Arrange
        $user = $this->createUser('find@example.com', [Role::ADMIN->value]);

        // Act
        $found = $this->userRepository()->findOneByUuid($user->getId());

        // Assert
        $this->assertNotNull($found);
        $this->assertSame($user->getId()->toRfc4122(), $found?->getId()->toRfc4122());
        $this->assertSame([Role::ADMIN->value, Role::USER->value], $found?->getRoles());
    }
}
