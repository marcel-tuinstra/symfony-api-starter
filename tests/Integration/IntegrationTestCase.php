<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->configureDatabaseUrl();
        static::bootKernel();
        $this->resetDatabaseSchema();
    }

    protected function container(): ContainerInterface
    {
        return static::getContainer();
    }

    protected function entityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->container()->get(EntityManagerInterface::class);

        return $entityManager;
    }

    private function configureDatabaseUrl(): void
    {
        if (isset($_ENV['DATABASE_URL']) && $_ENV['DATABASE_URL'] !== '') {
            return;
        }

        $projectDir = dirname(__DIR__, 2);
        $databasePath = $projectDir . '/var/cache/test/app.db';

        $directory = dirname($databasePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///' . $databasePath;
    }

    private function resetDatabaseSchema(): void
    {
        $entityManager = $this->entityManager();
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        if ($metadata === []) {
            return;
        }

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadata);
    }
}
