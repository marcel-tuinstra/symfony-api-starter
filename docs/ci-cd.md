# CI/CD Guide

CI is intentionally lightweight and focused on quality gates; deployment is left to downstream projects.

## Workflows (GitHub Actions)
- `lint.yml`: runs PHPStan, ECS, Rector (dry-run) on PRs and manual dispatch.
- `tests.yml`: runs PHPUnit suites (Unit/Integration/Functional) on SQLite, enforces 70% coverage, uploads `var/coverage/clover.xml`, and comments coverage on PRs.

## Quality Gates
- Coverage threshold: 70% (enforced in `tests.yml`).
- Static analysis and coding standards must pass (PHPStan, ECS, Rector dry-run).

## Local parity
- Lint locally: `make lint`
- Tests locally: `make test` (recreates test DB, loads fixtures), `make coverage` for HTML report.
- Export OpenAPI: `make openapi`

## Secrets & env in CI
- Dummy Keycloak/env defaults are provided in workflows; real deployments should override via GitHub Secrets (`COMPOSER_AUTH`, etc.).

## Future additions
- Deployment workflows are intentionally omitted in the starter; add project-specific pipelines (build/push images, stage/prod deploy) as needed.

## Related docs
- Setup: `docs/setup.md`
- Testing: `docs/testing.md`
- Architecture: `docs/architecture.md`
- Roadmap: `docs/roadmap.md`
