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

### **v0.0.3 — Observability & Logging**
**Goal:** Full visibility into runtime behaviour.

- [ ] Sentry integration (`sentry/sentry-symfony`)
- [ ] JSON Monolog logging + daily rotation
- [ ] Global exception → Problem+ JSON mapper
- [ ] Context-aware logging trait for Processors

---

### **v0.0.4 — Filtering & Pagination**
**Goal:** Queryable, flexible APIs.

- [ ] Global filters (`SearchFilter`, `OrderFilter`, `BooleanFilter`)
- [ ] Pagination defaults (25 / 100 max)
- [ ] Optional `#[MapQueryString]` usage for lightweight filtering
- [ ] Example `QueryFilterTrait`

---

### **v0.0.5 — Environment Maturity**
**Goal:** Predictable, safe, portable builds.

- [ ] Split config profiles (`dev`, `prod`, `test`)
- [ ] Optimized Dockerfile with Opcache
- [ ] `.env.dist` + `.env.test` examples
- [ ] Env variable validation
- [ ] Rate limiting (`symfony/rate-limiter`)
- [ ] Security headers & CORS hardening
- [ ] API version prefix `/api/v1/*`

---

### **v0.0.6 — Testing Discipline**
**Goal:** Maintain 70 %+ coverage with clear test layering.

- [ ] PHPUnit + ApiPlatform `ApiTestCase`
- [ ] `FunctionalTestCase` base class
- [ ] Unit / Integration / Functional split
- [ ] Coverage enforcement in CI
- [ ] Faker fixtures loaded for integration tests

---

### **v0.0.7 — CI/CD Automation**
**Goal:** Continuous confidence & deployment.

- [ ] GitHub Actions workflow (`ci.yml`)
- [ ] PHPStan, Rector, ECS, PHPUnit steps
- [ ] Coverage artifact upload
- [ ] Auto-deploy `develop` → staging via SSH
- [ ] Secrets: `DEPLOY_HOST`, `DEPLOY_PATH`, `DEPLOY_KEY`
- [ ] Optional Slack/Discord notifications

---

### **v0.0.8 — Documentation & Onboarding**
**Goal:** Self-explanatory developer experience.

- [ ] `/docs` folder with architecture, CI/CD, testing guides
- [ ] Auto-generated OpenAPI spec `/docs/openapi.json`
- [ ] README badges (CI, PHPStan, Coverage)
- [ ] Example Insomnia / Postman collection

---

### **v0.0.9 — Pre-Release Hardening**
**Goal:** Ship-quality baseline.

- [ ] Full type coverage (no `mixed`)
- [ ] Doctrine migrations test
- [ ] Healthcheck endpoint `/health`
- [ ] Docker healthchecks
- [ ] Blackfire / perf profile (optional)

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
