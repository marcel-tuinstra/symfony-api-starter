# Symfony API Starter

[![Lint](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/lint.yml/badge.svg?branch=develop&label=lint)](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/lint.yml)
[![Tests](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/tests.yml/badge.svg?branch=develop&label=tests)](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/tests.yml)
[![Coverage](https://img.shields.io/badge/coverage-%E2%89%A570%25-blue)](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/tests.yml)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%206-brightgreen)](https://phpstan.org/)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## About

This starter template provides a robust, scalable PHP API platform designed to accelerate backend development with modern tools and best practices. Built with a focus on maintainability, security, and developer experience, it integrates seamlessly with popular services like Keycloak for authentication and Sentry for error monitoring.

The philosophy centers on delivering a clean, extensible architecture that supports rapid iteration without sacrificing quality.

---

## Features

- **Symfony 7.4 + API Platform 4**: Attribute-driven resources, auto-registered controllers.
- **Keycloak OIDC (JWT)**: Token introspection with `KeycloakAuthenticator`; role-based access via `security.yaml`.
- **UUID IDs**: `symfony/uid` for entity identifiers.
- **Enum Roles**: `Role::USER` / `Role::ADMIN`.
- **Fixtures**: Faker-backed loaders for dev/tests; helpers for integration/functional tests.
- **Tooling**: PHPStan (level 6), ECS, Rector (dry-run), GrumPHP hooks.
- **Dockerized stack**: PHP-FPM, Postgres, Keycloak, Mailpit, Caddy.
- **Make targets**: `make up/down`, `make db-create migrate fixtures`, `make lint`, `make test`, `make openapi`.
- **CI Guardrails**: Split lint/test workflows, 70% coverage gate, coverage artifact/comment on PRs.

---

## Architecture Overview

This project follows a modular, layered architecture emphasizing separation of concerns and testability. For detailed insights and diagrams, please see the [Architecture Documentation](docs/architecture.md).

---

## Quick Start

See `docs/setup.md` for the full walkthrough. In short:

```bash
cp .env.dist .env.local
make up
make db-create migrate fixtures
make keycloak-refresh   # refresh JWKS from local Keycloak
make test
```

Common commands: `make lint`, `make coverage`, `make openapi`, `make down`.

## Documentation
- Setup & env: `docs/setup.md`
- Testing: `docs/testing.md`
- CI/CD: `docs/ci-cd.md`
- Architecture overview: `docs/architecture.md`
- Roadmap: `docs/roadmap.md`

## Endpoints & Auth
- API docs (Swagger UI): `/api/docs` (JSON: `/api/docs.json`)
- Healthcheck: `/health`
- Auth: Bearer JWT issued by Keycloak (see setup guide for realm/client configuration).

---

## Documentation 📚

Explore detailed guides and references:

- [Architecture](docs/architecture.md)
- [Testing](docs/testing.md)
- [CI/CD Pipeline](docs/ci-cd.md)
- [Roadmap](docs/roadmap.md)

---

## Contributing

We welcome contributions! Please review our [Contributing Guidelines](docs/contributing.md) before submitting issues or pull requests.

---

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## Author

Marcel Tuinstra — [GitHub Profile](https://github.com/marceltuinstra) — [Web](https://marcel.tuinstra.dev)
