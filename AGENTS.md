# Repository Guidelines

- This is a reusable starter template: favor standards-based defaults and avoid project-specific assumptions; leave room for downstream projects to opt into opinions (e.g., keep timestamps as top-level fields instead of nesting under `meta` unless a consumer explicitly requires it).

## Project Structure & Modules
- Core code lives in `src/` (domain, application, infrastructure) with API Platform resources and Symfony services; routes/controllers are auto-registered via attributes.
- Configuration sits under `config/`; environment secrets belong in `.env.local` (copy from `.env.dist`).
- Database migrations are in `migrations/`; Twig templates in `templates/`; public entrypoint at `public/index.php`.
- Tests are grouped by type in `tests/Unit`, `tests/Integration`, and `tests/Functional`; fixtures live under `src/DataFixtures`.
- Docker assets and service definitions are in `docker/` and `compose.yaml`; developer docs live in `docs/`.

## Build, Test, and Development Commands
- Start stack: `make up` (builds and boots PHP, Postgres, Keycloak, Mailpit); stop with `make down -v`.
- Database setup: `make db-create migrate fixtures`; generate diffs with `make migrate-diff`.
- Local dev loop: `make lint` (PHPStan, ECS, Rector dry-run) then `make test` (drops/recreates test DB, loads fixtures, runs PHPUnit).
- Coverage report: `make coverage` (outputs HTML to `var/coverage`).
- Style auto-fix: `make fix`; refresh Keycloak JWKS cert: `make keycloak-refresh`.
- Composer mirrors exist (`composer test`, `composer lint`, `composer fix`) if Docker is already running.

## Coding Style & Naming Conventions
- Follow PSR-12 and project conventions enforced by EasyCodingStandard, Rector, and PHPStan (Level 6). Run `make lint` before pushing.
- Use PHP 8.2+ features (typed properties, readonly where possible, constructor promotion). Prefer UUIDs from `symfony/uid`.
- Prefer Symfony/ObjectMapper with API Platform custom input/output DTOs; fall back to serialization groups (`<entity>:read` / `<entity>:write`) only when DTO mapping cannot cover the scenario. Roles use `Role::USER` / `Role::ADMIN`.
- Name tests with clear intent (e.g., `UserRegistrationTest`), services with explicit suffixes (`*Service`, `*Repository`).
- Import built-ins like `DateTime`, `DateTimeImmutable`, and `Exception` instead of referencing them with leading backslashes.

## Testing Guidelines
- Use AAA style with explicit `// Arrange`, `// Act`, `// Assert` comments in tests.
- Target ≥70% coverage; include unit coverage for pure logic and integration/functional tests for persistence and HTTP contracts.
- Use `tests/Functional` for API Platform endpoints (e.g., `UserApiTest`), `tests/Integration` for repositories/services hitting the DB, and `tests/Unit` for isolated domain logic.
- Reset state with `make prepare-test-db` when debugging DB-related failures.

## Commit & Pull Request Guidelines
- Prefer Conventional Commits (`feat: ...`, `fix: ...`, `chore: ...`, `docs: ...`); keep scopes concise (`feat(api)`).
- Small, focused commits are easier to review; include migration files and fixtures updates when schema changes.
- Pull Requests should describe intent, link issues, list major changes, and note testing (`make lint`, `make test`). Add screenshots or sample payloads for API changes when relevant.

## Security & Configuration Tips
- Never commit `.env.local`, secrets, or generated keys. Refresh the Keycloak public key after realm changes via `make keycloak-refresh`.
- Use `xdebug-on` / `xdebug-off` targets only when needed to keep containers lean; clean caches with `make clean` if Symfony behaves unexpectedly.
