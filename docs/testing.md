# Testing Guide for Symfony API Starter

## Introduction: Our Testing Philosophy

Testing is a critical part of building reliable, maintainable, and scalable APIs. At Symfony API Starter, we emphasize writing clear, concise, and meaningful tests that verify the behavior of your application at multiple levels. Our goal is to catch bugs early, ensure API contracts remain stable, and provide confidence when refactoring or adding new features.

---

## Types of Tests

We organize our tests into three main categories:

- **Unit Tests**: Test individual classes or methods in isolation. Fast and focused on logic without external dependencies.
- **Integration Tests**: Verify interactions between components, such as database queries or service integrations.
- **Functional Tests**: Test the API endpoints end-to-end, simulating real HTTP requests and responses.

Each type plays a complementary role in ensuring full coverage and robustness.

---

## Tools We Use

- **PHPUnit**: The foundational testing framework for PHP.
- **ApiPlatform's `ApiTestCase`**: Provides utilities to test API endpoints with ease.
- **FakerPHP**: For generating realistic fake data during tests.
- **Prophecy / Mockery**: Libraries for mocking dependencies and creating test doubles.

These tools empower you to write expressive and maintainable tests.

---

## Directory Structure for Tests

Our tests live under the `tests/` directory, organized by type:

```
tests/
├── Unit/
│   └── Service/
├── Integration/
│   └── Repository/
└── Functional/
    └── Api/
```

This structure helps keep tests organized and easy to navigate.

---

## Example Test Case: `UserApiTest`

Here's a simplified example of a functional test for the User API:

```php
<?php

namespace App\Tests\Functional\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class UserApiTest extends ApiTestCase
{
    public function testGetUsers(): void
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
        ]);
    }
}
```

This test verifies that the `/api/users` endpoint returns a successful response with the expected JSON structure.

---

## Running Tests

### Using Makefile

Run all tests with:

```bash
make test
```

This runs PHPUnit with coverage and other options configured.

### Using Composer Scripts

Alternatively, run:

```bash
composer test
```

This executes the same PHPUnit commands via Composer for convenience.

---

## CI Coverage Enforcement

We enforce a minimum test coverage of **70%** on all code. Our CI pipeline runs PHPUnit with coverage reports and fails the build if coverage drops below this threshold. This ensures continuous quality and helps prevent regressions.

---

## Best Practices for Writing Tests

- **Naming**: Use descriptive test method names, e.g., `testCreateUserWithValidData`.
- **Data Providers**: Use PHPUnit data providers to test multiple scenarios cleanly.
- **Mocking**: Mock external dependencies to isolate the code under test.
- **Avoid Over-Mocking**: Prefer real integration tests when possible to increase confidence.
- **Keep Tests Independent**: Tests should not depend on each other or shared state.
- **Use Faker**: Generate realistic test data rather than hardcoding values.

---

## Code Coverage Generation and Viewing

Run tests with coverage enabled:

```bash
php bin/phpunit --coverage-html var/coverage
```

Then open `var/coverage/index.html` in your browser to view detailed coverage reports, including uncovered lines and complexity.

---

## Optional Future Tools

- **PestPHP**: A delightful PHP Testing Framework with a focus on simplicity.
- **Infection**: Mutation testing to improve test suite effectiveness.
- **Static Analysis**: Tools like PHPStan or Psalm to catch errors before runtime.

These can be integrated progressively to enhance quality and developer experience.
