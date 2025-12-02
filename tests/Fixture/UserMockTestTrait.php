<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\User;
use App\Enum\User\Role;
use PHPUnit\Framework\MockObject\MockObject;

trait UserMockTestTrait
{
    /**
     * @param string[] $roles
     * @return User&MockObject
     */
    protected function createUserMock(string $email = 'user@example.com', array $roles = [Role::USER->value]): User
    {
        $user = $this->mock(User::class);
        $user->method('getEmail')->willReturn($email);
        $user->method('getUserIdentifier')->willReturn($email);
        $user->method('getRoles')->willReturn($roles);

        return $user;
    }

    /**
     * @param class-string $class
     */
    abstract protected function mock(string $class): MockObject;
}
