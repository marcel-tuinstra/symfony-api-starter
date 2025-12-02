<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

abstract class FunctionalTestCase extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->configureDatabaseUrl();
        $this->configureDefaultEnv();
        static::bootKernel();
        $this->resetDatabaseSchema();
    }

    protected function createApiClient(array $options = [], array $server = []): Client
    {
        return static::createClient($options, $server);
    }

    protected function entityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        return $entityManager;
    }

    /**
     * @param string[] $roles
     */
    protected function createAuthenticatedClient(string $email, array $roles): Client
    {
        $token = $email . '|' . implode(',', $roles);

        return $this->createApiClient()->withOptions([
            'auth_bearer' => $token,
        ]);
    }

    private function configureDatabaseUrl(): void
    {
        if (! empty($_ENV['DATABASE_URL'])) {
            return;
        }

        $projectDir = dirname(__DIR__, 2);
        $databasePath = $projectDir . '/var/cache/test/app_functional.db';

        $directory = dirname($databasePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $_ENV['DATABASE_URL'] = $_SERVER['DATABASE_URL'] = 'sqlite:///' . $databasePath;
    }

    private function configureDefaultEnv(): void
    {
        $defaults = [
            'CORS_ALLOW_ORIGIN' => '*',
            'KEYCLOAK_INTROSPECTION_URL' => 'https://keycloak.test/introspect',
            'KEYCLOAK_CLIENT_ID' => 'client-id',
            'KEYCLOAK_CLIENT_SECRET' => 'client-secret',
            'KEYCLOAK_BASE_URL' => 'https://keycloak.test',
            'KEYCLOAK_REALM' => 'test',
        ];

        foreach ($defaults as $key => $value) {
            if (empty($_ENV[$key])) {
                $_ENV[$key] = $_SERVER[$key] = $value;
            }
        }
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
