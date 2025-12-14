# Contributing to Symfony API Starter

Thank you for considering contributing to the Symfony API Starter project! Your help is invaluable in making this starter kit robust, maintainable, and easy to use for everyone. We believe in clean code, thorough testing, and collaborative development to build the best possible foundation for your Symfony APIs.

---

## 1. Development Setup

For full setup details see `docs/setup.md`. Quick start:

```bash
git clone https://github.com/marcel-tuinstra/symfony-api-starter.git
cd symfony-api-starter
make up
make db-create migrate fixtures
make keycloak-refresh   # optional: refresh JWKS from local Keycloak
```

Make sure you have Docker, PHP 8.3+, Composer, and Make installed.

---

## 2. Branching and Git Workflow

We follow a simple Git branching model:

- `main` — production-ready stable code.
- `develop` — integration branch for features and fixes.
- `feature/your-feature-name` — create these branches off `develop` for new features or bug fixes.

Workflow:

1. Pull the latest `develop` branch.
2. Create a feature branch: `git checkout -b feature/awesome-feature develop`
3. Commit your work regularly.
4. Push your branch and open a Pull Request (PR) against `develop`.
5. After review and approval, your PR will be merged.

---

## 3. Code Quality Standards

We enforce code quality using the following tools:

- **GrumPHP**: Runs pre-commit hooks for linting and tests.
- **PHPStan**: Static analysis to catch bugs early.
- **Rector**: Automated refactoring and upgrades.
- **Easy Coding Standard (ECS)**: Ensures PSR-12 compliance and coding style.

Run these before pushing:

```bash
make lint   # PHPStan + ECS + Rector (dry-run)
```

---

## 4. Commit Message Format

Please follow the [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) specification for commit messages:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

Examples:

- `feat(api): add user authentication endpoint`
- `fix(auth): correct token expiration time`
- `docs(readme): update contributing section`
- `chore(deps): upgrade symfony to 6.2`

---

## 5. Testing Requirements

Tests are critical! We require:

- Unit tests for all new logic.
- Integration tests for database and service interactions.
- Functional tests for API endpoints.
- Minimum code coverage of **70%**.

Run tests and coverage with:

```bash
make test
make coverage
```

See `docs/testing.md` for structure and patterns.

---

## 6. Changelog

Generate or refresh `CHANGELOG.md` before tagging a release:

```bash
composer changelog        # uses the latest tag as the baseline
composer changelog -- --since v0.0.5   # custom start point
```

---

## 7. Pull Request Guidelines

Before submitting a PR, please:

- Ensure all tests pass.
- Run code quality tools with no errors.
- Update documentation if necessary.
- Include a descriptive title and detailed description.
- Reference any related issues.
- Confirm your code adheres to the style guide.

---

## 8. Reporting Issues

When reporting bugs or requesting features, please include:

- A clear and descriptive title.
- Steps to reproduce the issue.
- Expected vs. actual behavior.
- Screenshots or logs if applicable.
- Environment details (PHP version, OS, Docker setup).

---

## 9. Style & Conventions

We adhere to:

- [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style.
- Meaningful, descriptive naming for classes, methods, and variables.
- Comprehensive PHPDoc blocks for classes and public methods.
- Consistent formatting and indentation.
