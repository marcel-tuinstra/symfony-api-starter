# Changelog
All notable changes to this project will be documented in this file.

## [0.1.0] - 2025-12-14
### Added
- Symfony 7.4 + API Platform 4 baseline with Keycloak JWT auth and role-based security.
- User API resource with DTO mapping, filters, custom provider, and soft-delete processor; Faker fixtures for dev/tests.
- Docker stack (PHP-FPM, Postgres, Keycloak, Mailpit, Caddy) with Make targets for up/down, migrations, fixtures, lint, tests, coverage, and OpenAPI export.
- Observability: Sentry integration, JSON Monolog logging, Problem+ exception mapper, healthcheck endpoint, rate limiting, pagination defaults.
- Testing and CI: unit/integration/functional suites, 70% coverage gate, split lint/test workflows, coverage artifact.
- Tooling: PHPStan level 6, ECS, Rector (dry-run), GrumPHP hooks, changelog generator (`composer changelog`).

### Docs
- Setup, testing, CI/CD, architecture, patterns (mailer, processors, ApiResource blueprint), roadmap, and changelog workflow.
