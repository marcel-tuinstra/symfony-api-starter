<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\User;
use App\Enum\User\Role;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

trait UserTestTrait
{
    /**
     * @param string[] $roles
     */
    protected function createUser(string $email = 'user@example.com', array $roles = [Role::USER->value]): User
    {
        $user = new User($email);
        $user->setRoles($roles);

        $entityManager = $this->entityManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $entityManager->refresh($user);

        return $user;
    }

    protected function reloadUser(User $user): User
    {
        $this->entityManager()->refresh($user);

        return $user;
    }

    protected function userRepository(): UserRepository
    {
        return $this->entityManager()->getRepository(User::class);
    }

    abstract protected function entityManager(): EntityManagerInterface;
}
