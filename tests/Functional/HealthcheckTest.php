<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class HealthcheckTest extends FunctionalTestCase
{
    public function testHealthEndpointIsAccessible(): void
    {
        // Arrange
        $client = $this->createApiClient();

        // Act
        $client->request('GET', '/health');

        // Assert
        $this->assertResponseIsSuccessful();
        $data = $client->getResponse()->toArray(false);
        $this->assertSame('ok', $data['status'] ?? null);
    }
}
