# 🧭 Symfony API Starter — Roadmap

A modern baseline for Symfony 7 + API Platform 4 applications.
Built for developer velocity, code quality, and modern SaaS backends.

---

## 🚀 Version Roadmap

### **v0.0.1 — Foundation Build**
**Goal:** Bootstrapped, minimal, working baseline.

- [x] Symfony 7 + API Platform 4
- [x] Keycloak OIDC authentication (JWT)
- [x] Single `User` ApiResource
- [x] FakerPHP-based fixtures
- [x] PHPStan level 6, Rector, EasyCodingStandard
- [x] GrumPHP pre-commit hook
- [x] Factory autowiring pattern via `supports()`
- [x] Docker Compose stack (PHP, PostgreSQL, Keycloak, Mailpit)
- [x] `.env.dist` with required variables

---

### **v0.0.2 — Developer Experience**
**Goal:** Frictionless DX and consistent coding conventions.

- [x] Symfony CLI + Xdebug toggle in Docker
- [x] Makefile commands (`up`, `down`, `lint`, `test`, `fixtures`)
- [x] Composer scripts for linting & analysis
- [x] NelmioCorsBundle configured for `localhost:3000`
- [x] UUID primary keys via `ramsey/uuid-doctrine`
- [x] Enum-based roles (`Role::USER`, `Role::ADMIN`)
- [x] Standardized serialization groups (`<entity>:read`, etc.)

---

### **v0.0.3 — Authentication & Secure CRUD**
**Goal:** Enable authenticated, role-based API access with full CRUD on User.

- [x] Keycloak JWT integration (`lexik/jwt-authentication-bundle` or OIDC introspection)
- [x] Secure API routes via `security.yaml` (e.g. disable anonymous writes)
- [x] Enable `POST`, `PUT`, `DELETE` for `User` (with `is_granted('ROLE_ADMIN')`)
- [x] Admin + regular user fixtures for testing
- [x] Updated API docs showing secured endpoints
- [x] README update with token usage and realm setup

---

### **v0.0.4 — Observability, Filtering & Environment Maturity**
**Goal:** Full visibility into runtime behaviour and robust, production-like configuration.

- [x] Sentry integration (`sentry/sentry-symfony`)
- [x] JSON Monolog logging + daily rotation
- [x] Global exception → Problem+ JSON mapper
- [ ] ~~Context-aware logging trait for Processors~~
- [X] Global filters (`SearchFilter`, `OrderFilter`, `BooleanFilter`)
- [x] Pagination defaults (25 $/ 100 max)
- [x] Optional `#[MapQueryString]` usage for lightweight filtering
- [x] Example `QueryFilterTrait`
- [x] Split config profiles (`dev`, `prod`, `test`)
- [x] Optimized Dockerfile with Opcache
- [x] `.env.dist` + `.env.test` examples
- [ ] ~~Env variable validation~~
- [x] Rate limiting (`symfony/rate-limiter`)
- [ ] ~~Security headers & CORS hardening~~
- [ ] ~~API version prefix `/api/v1/*`~~

---

### **v0.0.5 — Testing, CI/CD & Documentation**
**Goal:** High confidence, automated delivery, and clear developer onboarding.

- [ ] PHPUnit + ApiPlatform `ApiTestCase`
- [ ] `FunctionalTestCase` base class
- [ ] Functional tests for authentication & authorization
- [ ] Unit / Integration / Functional split
- [ ] Coverage enforcement in CI
- [ ] Faker fixtures loaded for integration tests
- [ ] PHPStan, Rector, ECS, PHPUnit steps
- [ ] Full type coverage (no `mixed`)
- [ ] Doctrine migrations test
- [ ] Healthcheck endpoint `/health`
- [ ] Docker healthchecks
- [ ] `/docs` folder with architecture, CI/CD, testing guides
- [ ] Auto-generated OpenAPI spec `/docs/openapi.json`
- [ ] README badges (CI, PHPStan, Coverage)
- [ ] Example Insomnia / Postman collection

---

### **v0.0.6 — CI/CD & Deployment**
**Goal:** Continuous integration and deployment pipeline for automated builds, testing, and staging delivery.

- [ ] GitHub Actions workflow (`ci.yml`)
- [ ] Coverage artifact upload
- [ ] Auto-deploy `develop` → staging via SSH
- [ ] Secrets: `DEPLOY_HOST`, `DEPLOY_PATH`, `DEPLOY_KEY`

---

### **v0.1.0 — Developer-Ready Baseline**
**Goal:** Clone-and-build starter for all future APIs.

- [ ] Complete public documentation
- [ ] Version tags & semantic release
- [ ] `CHANGELOG.md` generation
- [ ] Example Mailer & Processor patterns
- [ ] Ready for extension via new ApiResources

---

### Post-v0.1.0 Milestones
| Version | Focus |
|----------|--------|
| **0.2.0** | Multi-tenant architecture |
| **0.3.0** | Async processing (Messenger) |
| **0.4.0** | Mercure / WebSockets |
| **0.5.0** | i18n / translation |
| **0.6.0** | K8s readiness |
| **0.7.0** | Observability stack (Prometheus / Grafana) |
| **1.0.0** | Production-grade, documented starter |
