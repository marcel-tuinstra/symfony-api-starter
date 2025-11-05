# Symfony API Starter CI/CD Guide

Welcome to the comprehensive guide for setting up a modern, efficient, and robust CI/CD pipeline for your Symfony API Starter project. This guide will walk you through the philosophy, workflows, and practical implementation details to ensure your code quality, testing, and deployment processes are seamless and automated.

---

## Overview: The CI/CD Philosophy & Goals

Continuous Integration (CI) and Continuous Deployment (CD) are essential practices to maintain software quality and accelerate delivery. Our goals:

- **Automate** testing, linting, and deployment processes.
- **Ensure code quality** with linting and static analysis.
- **Run tests** and measure coverage to avoid regressions.
- **Deploy automatically** to staging and production environments.
- **Integrate monitoring** and error tracking with Sentry.
- **Enable rollback** and version tagging for safe releases.
- **Maintain transparency** with quality gates and status checks.

---

## CI Workflow Description (GitHub Actions)

We use **GitHub Actions** as our CI/CD platform. Every push or pull request triggers workflows that:

1. **Lint** code to catch syntax and style issues.
2. **Run tests** with PHPUnit and collect coverage data.
3. **Run static analysis** with PHPStan.
4. **Deploy** automatically based on branch rules (`develop` → staging, `main` → production).
5. **Notify Sentry** of releases for error monitoring.

---

## Step-by-Step Breakdown of Jobs

### 1. Linting

- Uses `phpcs` or `php-cs-fixer` to ensure PSR-12 coding standards.
- Fails early if code style issues are detected.

### 2. Testing

- Runs PHPUnit tests.
- Generates code coverage reports.
- Ensures all tests pass before deployment.

### 3. Coverage & Quality Gates

- Checks code coverage thresholds (e.g., minimum 70%).
- Runs PHPStan for static code analysis.
- Fails the build if quality gates are not met.

### 4. Deployment

- Deploys to **staging** on pushes to `develop`.
- Deploys to **production** on pushes to `main`.
- Uses environment variables and secrets for credentials.
- Supports rollback via Git tags and release management.

---

## Example GitHub Actions Workflow YAML

```yaml
name: Symfony API CI/CD

on:
  push:
    branches:
      - develop
      - main
  pull_request:
    branches:
      - develop
      - main

jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Install PHP and dependencies
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mbstring, intl
          tools: composer
      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-progress --no-suggest --no-interaction
      - name: Run PHP-CS-Fixer
        run: vendor/bin/php-cs-fixer fix --dry-run --diff
      - name: Run PHPStan
        run: vendor/bin/phpstan analyse -l max src tests

  test:
    runs-on: ubuntu-latest
    needs: lint
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          coverage: xdebug
      - name: Install dependencies
        run: composer install --prefer-dist --no-progress --no-suggest --no-interaction
      - name: Run PHPUnit tests
        run: vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml
      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v3
        with:
          files: coverage.xml
          fail_ci_if_error: true

  deploy:
    runs-on: ubuntu-latest
    needs: test
    if: github.ref == 'refs/heads/develop' || github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v3
      - name: Setup SSH
        uses: webfactory/ssh-agent@v0.5.4
        with:
          ssh-private-key: ${{ secrets.SSH_PRIVATE_KEY }}
      - name: Deploy to server
        run: |
          if [ "${{ github.ref }}" == "refs/heads/develop" ]; then
            ssh user@staging.example.com "cd /var/www/symfony-api && git pull && composer install && php bin/console cache:clear"
          elif [ "${{ github.ref }}" == "refs/heads/main" ]; then
            ssh user@production.example.com "cd /var/www/symfony-api && git pull && composer install && php bin/console cache:clear"
          fi
      - name: Create Git Tag for release
        if: github.ref == 'refs/heads/main'
        run: |
          TAG="v$(date +'%Y.%m.%d.%H%M%S')"
          git tag $TAG
          git push origin $TAG
      - name: Notify Sentry Release
        if: github.ref == 'refs/heads/main'
        env:
          SENTRY_AUTH_TOKEN: ${{ secrets.SENTRY_AUTH_TOKEN }}
          SENTRY_ORG: your-org
          SENTRY_PROJECT: your-project
        run: |
          sentry-cli releases new $TAG
          sentry-cli releases set-commits --auto $TAG
          sentry-cli releases finalize $TAG
```

---

## Secrets Management & Environment Variables

- Store sensitive data such as SSH keys, Sentry tokens, and API keys in **GitHub Secrets**.
- Access them securely in workflows using `${{ secrets.SECRET_NAME }}`.
- Use environment variables in your Symfony `.env` or `.env.local` for database credentials, API keys, etc.
- Never commit secrets to source control.

---

## Auto Deployment Strategy

- **Develop branch**: Auto-deploys to **staging** environment for QA and integration testing.
- **Main branch**: Auto-deploys to **production** environment for live release.
- This separation ensures stability and controlled release flow.

---

## Rollback & Tagging Strategy

- Each production deployment creates a **timestamped Git tag**.
- Tags allow quick rollback by checking out previous stable releases.
- Rollbacks can be automated or manual by deploying a previous tag.
- Keep tags consistent and descriptive (e.g., `v2024.06.01`).

---

## Quality Gates

- Minimum **code coverage** threshold enforced (e.g., 70%).
- PHPStan level 7 for static analysis errors.
- Linting must pass without errors.
- Builds fail if quality gates are not met, preventing broken code from deploying.

---

## Integration with Sentry for Release Tracking

- Sentry CLI is used to create releases and associate commits.
- Errors are tracked per release, enabling quick identification of issues.
- Automated release tagging in Sentry matches Git tags.
- Helps monitor production health and track regressions.

---

## Future Improvements

- **Docker build & push**: Containerize app and push images to registry.
- **Caching dependencies**: Speed up CI by caching Composer and PHP extensions.
- **Blue-green deployments**: Zero downtime deploys with traffic switching.
- **Infrastructure as Code**: Automate server provisioning with Terraform or Ansible.
- **Performance testing**: Integrate load testing in CI pipeline.