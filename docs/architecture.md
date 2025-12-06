# 🏗 Architecture Overview

Current, practical view of the starter.

## System shape
- **API Platform 4 + Symfony 7.4** for resources and HTTP stack.
- **Security**: Keycloak-issued JWTs validated by `KeycloakAuthenticator`; API routes locked down in `config/packages/security.yaml`.
- **Data**: Doctrine ORM, Postgres (Docker); UUID ids via `symfony/uid`; soft-delete handled by `SoftDeleteProcessor`; Faker fixtures for dev/tests.
- **Observability**: Sentry bundle, JSON Monolog logs, healthcheck at `/health`.
- **Tooling**: PHPStan, ECS, Rector (dry-run in CI), PHPUnit suites with 70% coverage gate.
- **Stack**: Docker Compose services (PHP-FPM, Postgres, Keycloak, Mailpit, Caddy).

## Code layout (highlights)
```
src/
  Api/           # DTOs / input mapping
  Controller/    # Attribute controllers (e.g., healthcheck)
  Entity/        # Doctrine entities (User + traits)
  Enum/          # Roles
  Security/      # Authenticators
  Service/       # External integrations (Keycloak)
  State/         # Processors/providers (soft delete, collections)
  DataFixtures/  # Faker fixtures
config/          # Symfony, API Platform, security
docker/          # Compose assets (PHP, Postgres, Keycloak, Mailpit, Caddy)
tests/           # Unit / Integration / Functional suites + helpers
docs/            # Guides (setup, testing, CI/CD, roadmap, architecture)
```

## Request/auth flow
```
Client -> /api/* with Bearer JWT
  security.yaml access_control -> KeycloakAuthenticator (introspection)
  User created/loaded & roles set -> ApiPlatform resource handling
```

## Data flow (User)
- Entity: `src/Entity/User.php` with UUID, email, roles, timestamps.
- Operations: API Platform exposes CRUD; delete uses `SoftDeleteProcessor`.
- Fixtures: Faker-driven fixtures for dev/tests; integration tests seed via traits.

## CI/CD posture
- GitHub Actions split:
  - `lint.yml`: PHPStan, ECS, Rector (dry-run)
  - `tests.yml`: PHPUnit Unit/Integration/Functional on SQLite, coverage upload + PR comment, 70% gate
- OpenAPI export via `make openapi` → `docs/openapi.json`.

## Cross-references
- Setup & env: `docs/setup.md`
- Testing: `docs/testing.md`
- CI/CD: `docs/ci-cd.md`
- Roadmap: `docs/roadmap.md`
