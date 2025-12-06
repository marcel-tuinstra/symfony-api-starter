<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\User;
use App\Enum\User\Role;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

trait FakerFixturesTrait
{
    /**
     * @return User[]
     */
    protected function loadFakerFixtures(int $userCount = 5): array
    {
        $faker = Factory::create();
        $users = [];

        $admin = new User($faker->unique()->safeEmail());
        $admin->setRoles([Role::ADMIN->value, Role::USER->value]);
        $users[] = $admin;

        for ($i = 0; $i < $userCount; $i++) {
            $user = new User($faker->unique()->safeEmail());
            $user->setRoles([Role::USER->value]);
            $users[] = $user;
        }

        $entityManager = $this->entityManager();

        foreach ($users as $user) {
            $entityManager->persist($user);
        }

        $entityManager->flush();

        foreach ($users as $user) {
            $entityManager->refresh($user);
        }

        return $users;
    }

    abstract protected function entityManager(): EntityManagerInterface;
}
