# CI/CD Guide

CI is intentionally lightweight and focused on quality gates. This starter ships an opt-in staging deploy workflow for downstream projects; the starter itself does not set staging secrets or deploy anywhere.

## Workflows (GitHub Actions)
- `lint.yml`: runs PHPStan, ECS, Rector (dry-run) on PRs and manual dispatch.
- `tests.yml`: runs PHPUnit suites (Unit/Integration/Functional) on SQLite, enforces 70% coverage, uploads `var/coverage/clover.xml`, and comments coverage on PRs.
- `deploy-staging.yml`: on `develop` or manual dispatch, rsyncs the repository to a staging host over SSH and runs `composer install --no-dev --optimize-autoloader` remotely. The job skips automatically if secrets are absent so the starter can be used without staging. Downstream projects can extend this with cache warmups or service reloads (`php bin/console cache:clear --env=prod`, `php bin/console doctrine:migrations:migrate`, `sudo systemctl reload php-fpm`, etc.).

## Quality Gates
- Coverage threshold: 70% (enforced in `tests.yml`).
- Static analysis and coding standards must pass (PHPStan, ECS, Rector dry-run).

## Local parity
- Lint locally: `make lint`
- Tests locally: `make test` (recreates test DB, loads fixtures), `make coverage` for HTML report.
- Export OpenAPI: `make openapi`

## Secrets & env in CI
- Dummy Keycloak/env defaults are provided in workflows; real deployments should override via GitHub Secrets (`COMPOSER_AUTH`, etc.).
- Staging deploy requires GitHub Secrets. Example values for a downstream project:
  - `DEPLOY_HOST`: `deployer@example.com` (SSH user + host)
  - `DEPLOY_PATH`: `/var/www/my-api` (remote absolute path where the repo is synced)
  - `DEPLOY_KEY`: private key that allows SSH/rsync to `DEPLOY_HOST` (no passphrase or use an SSH agent on the host)
  With these set, pushes to `develop` (or a manual `Run workflow`) will sync code to staging and install prod dependencies.
  The starter intentionally leaves these unset; downstream projects should add them when they want staging delivery.

## Related docs
- Setup: `docs/setup.md`
- Testing: `docs/testing.md`
- Architecture: `docs/architecture.md`
- Roadmap: `docs/roadmap.md`
