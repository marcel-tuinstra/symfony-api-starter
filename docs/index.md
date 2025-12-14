# Public Documentation

Use this starter as a drop-in baseline for new APIs. Below is the entry point to all docs.

## Quick Links
- Setup & environment: `docs/setup.md`
- Architecture: `docs/architecture.md`
- Testing strategy: `docs/testing.md`
- CI/CD workflows: `docs/ci-cd.md`
- Changelog process: `docs/changelog.md`
- Patterns (mailer & processors): `docs/patterns.md`
- Roadmap: `docs/roadmap.md`
- Contributing guide: `docs/contributing.md`

## Working locally
- Spin up the stack: `make up` (PHP, Postgres, Keycloak, Mailpit, Caddy).
- Apply migrations and fixtures: `make db-create migrate fixtures`.
- API docs: `/api/docs`; healthcheck: `/health`.
- Mailpit for local email: SMTP `smtp://mailpit:1025` (or `localhost:1026`), UI at `http://localhost:8026`. Send a test message with `php bin/console app:mail:test --to dev@example.com`.

## Release hygiene
- Generate changelog: `composer changelog` (uses the latest tag as baseline).
- Keep Conventional Commits to ensure clean grouping in changelog.
- Tag releases with `git tag vX.Y.Z && git push --tags`.
