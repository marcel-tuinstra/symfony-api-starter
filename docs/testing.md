# Testing Guide

How we structure and run tests in the starter.

## Suites & Conventions
- **Unit** (`tests/Unit`): isolated logic; use PHPUnit mocks.
- **Integration** (`tests/Integration`): real DB (SQLite in test env), repositories/services; base class resets schema per test.
- **Functional** (`tests/Functional`): HTTP-level checks using `ApiPlatform\Symfony\Bundle\Test\ApiTestCase`; test authenticator simplifies bearer tokens.
- Naming: `*Test` with AAA comments (`// Arrange`, `// Act`, `// Assert`).
- Fixtures: Faker-based seeders for integration/functional via `FakerFixturesTrait`.

## Base classes
- `App\Tests\Unit\UnitTestCase`: helpers for mocks/configured mocks.
- `App\Tests\Integration\IntegrationTestCase`: boots kernel, sets SQLite DB, recreates schema before each test.
- `App\Tests\Functional\FunctionalTestCase`: ApiTestCase with SQLite DB, default env, and helper for authenticated clients.

## Running tests
- All suites: `make test`
- Coverage (HTML): `make coverage` → `var/coverage`
- Per suite: `vendor/bin/phpunit --testsuite Unit|Integration|Functional`

## CI expectations
- GitHub Actions `tests.yml` runs all suites on SQLite.
- Coverage gate: 70% minimum (build fails below).

## Examples
- Functional: `tests/Functional/UserApiTest.php` exercises authz and CRUD.
- Healthcheck: `tests/Functional/HealthcheckTest.php`.
- Integration: `tests/Integration/Repository/UserRepositoryTest.php`.

## Related docs
- Setup: `docs/setup.md`
- CI/CD: `docs/ci-cd.md`
- Architecture: `docs/architecture.md`
