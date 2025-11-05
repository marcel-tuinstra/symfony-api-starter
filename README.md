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

## Features ✨

- **API Platform**: Fully-featured REST and GraphQL APIs with built-in validation and serialization.
- **Keycloak Integration**: Secure, standards-compliant authentication and authorization.
- **Sentry**: Real-time error tracking and monitoring.
- **Faker**: Generate realistic test data effortlessly.
- **GrumPHP**: Automated quality checks and pre-commit hooks to enforce code standards.
- **Dockerized Development**: Easy setup with Docker Compose for consistent environments.
- **Comprehensive Testing**: PHPUnit and integration tests with coverage reports.

---

## Architecture Overview 🏗️

This project follows a modular, layered architecture emphasizing separation of concerns and testability. For detailed insights and diagrams, please see the [Architecture Documentation](docs/architecture.md).

---

## Quick Start ⚡

```bash
# Clone the repository
git clone https://github.com/marcel-tuinstra/symfony-api-starter.git
cd symfony-api-starter

# Start services with Docker Compose
make up

# Run database migrations
make migrate

# Load development fixtures
make fixtures

# Open the documentation in your browser
make docs
```

---

## Documentation 📚

Explore detailed guides and references:

- [Architecture](docs/architecture.md)
- [Testing](docs/testing.md)
- [CI/CD Pipeline](docs/ci-cd.md)
- [Roadmap](docs/roadmap.md)

---

## Contributing 🤝

We welcome contributions! Please review our [Contributing Guidelines](docs/contributing.md) before submitting issues or pull requests.

---

## License 📄

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## Author ✍️

Marcel Tuinstra — [GitHub Profile](https://github.com/marceltuinstra) — [Web](https://marcel.tuinstra.dev)
