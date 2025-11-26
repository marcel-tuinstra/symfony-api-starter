<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\KeycloakService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly KeycloakService $keycloakService
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();

        $admin = new User($generator->unique()->safeEmail());
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->keycloakService->createUserInKeycloak($admin->getEmail(), $admin->getRoles());

        for ($i = 0; $i < 10; $i++) {
            $user = new User($generator->unique()->safeEmail());
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->keycloakService->createUserInKeycloak($user->getEmail(), $user->getRoles());
        }

        $manager->flush();
    }
}
