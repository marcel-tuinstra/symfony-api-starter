# Symfony API Starter

[![GitHub Actions CI](https://github.com/marcel-tuinstra/symfony-api-starter/actions/workflows/ci.yml/badge.svg)](https://github.com/marcel-tuinstra/symfony-api-starter/actions)
[![PHPStan Level](https://img.shields.io/badge/PHPStan-Level%207-brightgreen)](https://phpstan.org/)
[![Coverage](https://img.shields.io/badge/coverage-70%25-brightgreen)](https://github.com/username/projectname/actions)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

---

## About

ProjectName is a robust, scalable PHP API platform designed to accelerate backend development with modern tools and best practices. Built with a focus on maintainability, security, and developer experience, it integrates seamlessly with popular services like Keycloak for authentication and Sentry for error monitoring.

The philosophy centers on delivering a clean, extensible architecture that supports rapid iteration without sacrificing quality.

---

## Features

- **Symfony 7 + API Platform 4**: Clean, modern foundation for RESTful and GraphQL APIs.
- **Keycloak OIDC Authentication (JWT)**: Secure and standards-compliant authentication.
- **UUID Primary Keys**: Consistent, collision-resistant entity IDs via `ramsey/uuid-doctrine`.
- **Enum-Based Roles**: Standardized authorization using `Role::USER`, `Role::ADMIN`.
- **FakerPHP Fixtures**: Quick and reliable seeding of realistic data for development.
- **Developer Tooling**: PHPStan (Level 6), Rector, EasyCodingStandard, GrumPHP pre-commit hooks.
- **Serialization Groups**: Standardized `<entity>:read` and `<entity>:write` patterns for clarity.
- **Dockerized Stack**: Full local setup including PHP, PostgreSQL, Keycloak, and Mailpit.
- **Makefile Commands**: Streamlined workflow (`up`, `down`, `lint`, `test`, `fixtures`).
- **Composer Scripts**: Unified linting, analysis, and testing shortcuts.
- **Xdebug + Symfony CLI**: Frictionless DX for debugging and local inspection.
- **CORS Configuration**: NelmioCorsBundle configured for `localhost:3000` (frontend ready).

---

## Architecture Overview

This project follows a modular, layered architecture emphasizing separation of concerns and testability. For detailed insights and diagrams, please see the [Architecture Documentation](docs/architecture.md).

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/marcel-tuinstra/symfony-api-starter.git
cd symfony-api-starter

# 2. Copy environment configuration
cp .env.dist .env

# 3. Start the full Docker stack (PHP, Postgres, Keycloak, Mailpit)
make up

# 4. Initialize the database schema and load fixtures
make fixtures

# 5. Run the test suite to verify setup
make test

# 6. Access the API documentation
open http://localhost:8080/api/docs
```

**Tips**
- Stop all containers: `make down`
- Run static analysis: `make lint`
- Auto-fix code style: `make fix`
- Toggle Xdebug on/off: `make xon` / `make xoff`
- View Symfony environment info: `make info`

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
