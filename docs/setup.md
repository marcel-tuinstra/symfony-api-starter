# 🛠️ Setup Guide — Symfony API Starter

A quick guide to get the stack running locally, configure Keycloak, and validate the API.

## Prerequisites
- Docker + Docker Compose
- Make
- PHP 8.3+ (for local tooling), Composer

## Using this starter for a new project
- Click “Use this template” on the repo (avoid forking to keep history clean), create your new repo (e.g., `acme-saas-api`), then `git clone git@github.com:<you>/acme-saas-api.git`.
- Copy envs: `cp .env.dist .env.local` and fill real values (DB, Keycloak realm/client, APP_SECRET).
- Bring it up: `make up` then `make db-create migrate fixtures`.
- Keep upstream handy: `git remote add upstream git@github.com:<org>/symfony-api-starter.git` so you can `git fetch upstream` and cherry-pick/rebase future fixes.
- CI secrets: add `COMPOSER_AUTH` (if needed) and optional deploy secrets in your new repo; deploy will skip without them.

## 1) Clone & Environment
```bash
git clone <repo> symfony-api-starter
cd symfony-api-starter
cp .env.dist .env.local   # adjust values as needed
```

Key .env variables to review:
- `APP_SECRET`
- `DATABASE_URL` (defaults to Postgres in Docker)
- `CORS_ALLOW_ORIGIN` (e.g. http://localhost:3000)
- Keycloak: `KEYCLOAK_BASE_URL`, `KEYCLOAK_REALM`, `KEYCLOAK_CLIENT_ID`, `KEYCLOAK_CLIENT_SECRET`, `KEYCLOAK_INTROSPECTION_URL`

## 2) Start the stack
```bash
make up
```
Services: PHP-FPM, Postgres, Keycloak, Mailpit, Caddy. Stop with `make down -v`.

## 3) Database & Fixtures
```bash
make db-create migrate fixtures
```

## 4) Keycloak configuration
Assumes the bundled Keycloak container on port 8180 with realm import from `docker/keycloak`.
- Realm: `symfony` (adjust to match `KEYCLOAK_REALM`)
- Client: set `KEYCLOAK_CLIENT_ID` / `KEYCLOAK_CLIENT_SECRET`; enable `service accounts` and `client credentials` for introspection.
- Roles: `ROLE_USER`, `ROLE_ADMIN` as realm roles.
- Service account roles: on the client, assign `ROLE_USER` (and `ROLE_ADMIN` if needed) to the Service Account Roles tab so client-credential tokens include the API roles out of the box.
- JWKS refresh: after realm changes run `make keycloak-refresh` to update `config/jwt/keycloak_public.pem`.
- Introspection URL example: `http://keycloak:8180/realms/symfony/protocol/openid-connect/token/introspect` (match in `.env.local` as `KEYCLOAK_INTROSPECTION_URL`).

## 5) Local dev loop
- Lint: `make lint` (PHPStan, ECS, Rector dry-run)
- Tests: `make test` (drops/recreates test DB, loads fixtures, runs PHPUnit suites)
- Coverage: `make coverage` (HTML in `var/coverage`)
- OpenAPI export: `make openapi` (writes `docs/openapi.json`)

## 6) Health & Docs
- Healthcheck: `GET /health`
- API docs: Swagger UI at `/api/docs`, JSON at `/api/docs.json`
- OpenAPI file: `docs/openapi.json` (run `make openapi` to refresh)

## 7) Useful references
- Architecture: `docs/architecture.md`
- CI/CD: `docs/ci-cd.md`
- Testing: `docs/testing.md`

## 8) Troubleshooting
- Symfony cache issues: `make clean`
- DB issues: `make prepare-test-db` for test DB reset
- Keycloak cert mismatch: `make keycloak-refresh`
