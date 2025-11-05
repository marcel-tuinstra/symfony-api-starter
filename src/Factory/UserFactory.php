<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;

final class UserFactory implements FactoryInterface
{
    public function supports(string $type): bool
    {
        return $type === User::class;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data = []): User
    {
        $user = new User($data['email'] ?? 'example@example.com');
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);
        return $user;
    }
}
