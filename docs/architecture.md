# 🏗Architecture Overview

> **Your Ultimate Guide to Building Modern Symfony + API Platform Backends**  
> _Opinionated, scalable, and developer-friendly._

---

## Philosophy & Design Goals

We believe in building backends that are:

- **Robust & Maintainable:** Leveraging strict typing and static analysis to catch issues early.
- **API-First:** Designing every feature as a first-class API resource.
- **Extensible & Modular:** Using factories and providers to keep code loosely coupled.
- **Secure & Stateless:** Delegating identity to Keycloak while Symfony enforces token validation.
- **Developer-Centric:** Minimal boilerplate, clear conventions, and powerful tooling to boost productivity.
- **Infrastructure as Code:** Mirror production environments locally with Docker Compose.
- **Fail Fast, Fail Loud:** Encourage early error detection and clear feedback loops.

---

## Architecture Diagram

```text
+-------------------+      +------------------+      +-------------------+
|                   |      |                  |      |                   |
|  Client / Frontend | <--> |  API Platform    | <--> |  Symfony Backend  |
|                   |      |  (API Gateway)   |      |  (Business Logic) |
+-------------------+      +------------------+      +-------------------+
                                  |                          |
                                  v                          v
                          +----------------+        +-----------------+
                          | Keycloak (OIDC)|        | Postgres DB     |
                          +----------------+        +-----------------+
                                  |
                                  v
                          +----------------+
                          | Mailpit / SMTP |
                          +----------------+
```

---

## Core Principles & Components

### 1. API-First Design

All domain models are exposed as **`#[ApiResource]`** annotated entities, enabling automatic CRUD endpoints and rich API features.

```php
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    security: "is_granted('ROLE_USER')"
)]
class User {
    // ...
}
```

### 2. Controllerless Architecture

No traditional controllers! Business logic is encapsulated in:

- **`State\Processor`** — Handles commands, mutations, and side effects.
- **`State\Provider`** — Handles queries and data retrieval.

Example Processor:

```php
final class UserRegistrationProcessor implements ProcessorInterface
{
    public function process($data, string $operationName, array $context): object
    {
        // Registration logic here
    }
}
```

### 3. Stateless Authentication

- Keycloak issues JWT tokens.
- Symfony validates tokens and claims.
- User entities are hydrated lazily on first request.
- Authorization via `is_granted()` annotations or voters.

### 4. Factory-Based Extensibility

Services implement a `supports()` method for dynamic discovery and creation.

```php
interface FactoryInterface {
    public function supports(string $type): bool;
    public function create(array $data): object;
}
```

Example usage:

```php
if ($factory->supports('user')) {
    $user = $factory->create($data);
}
```

### 5. Configuration by Convention

Sensibly chosen defaults reduce boilerplate. Override only when necessary.

### 6. Infrastructure-as-Code

Docker Compose setup replicates production, including:

- PHP 8.3
- Postgres
- Keycloak
- Mailpit
- Optional Caddy web server

### 7. Fail Fast, Fail Loud

Strict typing, static analysis (PHPStan), Rector, and ECS enforce quality and modernization.

---

## Authentication Flow Diagram

```text
+------------------+     +-----------------+     +---------------------+
|                  |     |                 |     |                     |
|    Client App    | --> |  Symfony Backend| --> |     Keycloak OIDC   |
| (Frontend or CLI)|     | (JWT Validation)|     | (Identity Provider) |
+------------------+     +-----------------+     +---------------------+
         |                         |                       |
         |                         |                       |
         |                         |                       |
         +-------------------------+-----------------------+
                                   |
                                   v
                           +-----------------+
                           |  User Database  |
                           +-----------------+
```

---

## Directory Structure

```text
src/
  Entity/           # Domain models & ApiResources
  Factory/          # Factories for extensibility
  Mailer/           # Email sending services
  Security/         # Custom voters & security logic
  State/            # Processors & Providers (business logic)
  DataFixtures/     # Test & dev data loaders
  EventSubscriber/  # Event-driven logic
  Enum/             # Enumerations & constants
  Contract/         # Interfaces & shared contracts

config/
  packages/         # Symfony config files
    dev/
    prod/
    test/

docker/
  keycloak/         # Keycloak server config
  postgres/         # Postgres DB config
  mailpit/          # Mailpit SMTP config

docs/
  roadmap.md
  architecture.md
  testing.md
  ci-cd.md
```

---

## Tech Stack Overview

| Category           | Technology / Tool       | Purpose                       |
|--------------------|------------------------|-------------------------------|
| Framework          | Symfony 6.4+           | Backend framework              |
| API Layer          | API Platform           | Auto-generated REST/GraphQL   |
| Authentication     | Keycloak               | OpenID Connect provider       |
| Database           | PostgreSQL             | Relational DBMS               |
| Containerization   | Docker Compose         | Local & production environment|
| Email              | Mailpit / SMTP         | Email testing & sending       |
| Code Quality       | PHPStan, Rector, ECS   | Static analysis & code style  |
| Git Hooks          | GrumPHP                | Pre-commit checks             |
| Logging & Monitoring| Monolog, Sentry        | Observability & error tracking|
| Web Server (optional)| Caddy                 | HTTPS reverse proxy           |

---

## Development Workflow

### Makefile Commands

- `make install` — Install dependencies and initialize environment.
- `make start` — Start Docker services.
- `make test` — Run PHPUnit tests.
- `make lint` — Run static analysis and code style checks.
- `make cs-fix` — Automatically fix coding style issues.
- `make migrate` — Run database migrations.
- `make fixtures` — Load development fixtures.

### CI Pipeline

- **Lint & Static Analysis:** PHPStan, ECS, GrumPHP run on every push.
- **Unit & Integration Tests:** PHPUnit coverage checks.
- **Build & Deploy:** Automated Docker image builds and deployments.
- **Security Scans:** Dependency vulnerability checks.

### Git Branching Model

- **`main`** — Production-ready code.
- **`develop`** — Integration branch for features.
- **`feature/*`** — New features.
- **`hotfix/*`** — Urgent fixes.
- **`release/*`** — Pre-release stabilization.
