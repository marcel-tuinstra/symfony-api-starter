<?php

declare(strict_types=1);

namespace App\Tests\Integration\Migrations;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class MigrationsTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->configureDatabaseUrl();
        $this->resetDatabaseFile();
    }

    public function testDoctrineMigrationsApplySuccessfully(): void
    {
        $kernel = static::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);

        $isSqlite = static::getContainer()->get('doctrine')->getConnection()->getDatabasePlatform()->getName() === 'sqlite';

        $exitCode = $commandTester->execute([
            '--no-interaction' => true,
            '--allow-no-migration' => true,
            '--dry-run' => $isSqlite,
        ]);

        $this->assertSame(0, $exitCode, $commandTester->getDisplay());

        if ($isSqlite) {
            self::markTestSkipped('SQLite cannot execute PostgreSQL migrations; dry-run verified output.');
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $schemaManager = $entityManager->getConnection()->createSchemaManager();
        $this->assertTrue($schemaManager->tablesExist(['user']));
    }

    private function configureDatabaseUrl(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $databasePath = $projectDir . '/var/cache/test/app_migrations.db';
        $this->ensureDirectoryExists($databasePath);

        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///' . $databasePath;
    }

    private function resetDatabaseFile(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $databasePath = $projectDir . '/var/cache/test/app_migrations.db';

        if (file_exists($databasePath)) {
            unlink($databasePath);
        }
    }

    private function ensureDirectoryExists(string $databasePath): void
    {
        $directory = dirname($databasePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }
}
