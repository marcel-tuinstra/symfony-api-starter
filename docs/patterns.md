# Patterns: Mailer & Processors

Quick-reference patterns to extend the starter safely.

## Adding a new ApiResource (blueprint)
- Create an entity under `src/Entity` (UUID `id`, timestamps via trait if needed).
- Create a resource DTO under `src/Api/Resource` and map it with `#[Map(source: Entity::class)]`.
- For writes, add an input DTO (e.g., `src/Api/Input/FooInput.php`) and set `input: FooInput::class` on write operations.
- Define operations with `ApiResource` (GET/POST/PATCH/DELETE) and assign providers/processors as needed; see `UserResource` for a full example (filters, custom provider, soft-delete processor).
- Validation groups: pass `validationContext` per operation; DTOs carry constraints.
- Keep controller-less flow: rely on providers/processors; prefer ObjectMapper for entity↔DTO mapping.

## Mailer (Mailpit-first)
- Env defaults: `MAILER_DSN=smtp://mailpit:1025`, `MAIL_FROM_ADDRESS=dev@example.com` (`.env.dist`).
- Test mailer service: `App\Service\TestMailer` with template `templates/email/test_email.html.twig`.
- Sanity check: `php bin/console app:mail:test --to you@example.com` (appears in Mailpit UI at http://localhost:8026).
- For local CLI without Docker, override DSN: `MAILER_DSN=smtp://localhost:1026 php bin/console app:mail:test`.
- For downstream projects, swap DSN in `.env.prod.local` (e.g., SendGrid, SES) without touching code.

## ApiPlatform processors
- Processors add side effects around persistence (logging, events, mail, async).
- Soft delete example: `App\State\Processor\SoftDeleteProcessor` (used by `UserResource` delete). It mutates the entity, calls the persist processor, then maps back to a resource DTO via ObjectMapper.
- Wiring example (from `UserResource`): `new Delete(..., processor: SoftDeleteProcessor::class)`.
- Testing tip: mock `ProcessorInterface` for unit tests; in functional tests assert DB state and side effects (e.g., logs, events, outbound mails via mailer test transport).
