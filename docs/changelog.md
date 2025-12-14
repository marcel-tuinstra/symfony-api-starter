# Changelog Workflow

This starter ships with a simple changelog generator built on top of git history and Conventional Commits.

## Generate

- Default (uses the latest git tag as the starting point, or all history if none exist):

```bash
composer changelog
```

- Start from a specific tag or commit:

```bash
composer changelog -- --since v0.0.5
```

The command writes to `CHANGELOG.md` in the project root.

## Finalize & tag releases

- Promote `[Unreleased]` to a versioned section and reset `[Unreleased]`:

```bash
VERSION=v0.1.1 composer release:prepare
```

- Create a git tag (fails if the working tree is dirty):

```bash
VERSION=v0.1.1 composer release:tag
git push origin v0.1.1   # manual push
```

- One-shot convenience (prepare + tag):

```bash
VERSION=v0.1.1 composer release
```

- Publish flow (expects a clean tree with finalized changelog already committed):

```bash
VERSION=v0.1.1 composer release:publish
```

## How it works

- Scans commit subjects and groups them by type (`feat`, `fix`, `docs`, `chore`, etc.).
- Generates an `[Unreleased]` section with the detected changes and keeps existing release sections intact.
- Deduplicates repeated commit messages to keep the log tidy.

## Tips

- Keep using [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) so grouping stays accurate.
- Create lightweight tags (`git tag v0.1.0 && git push --tags`) to scope the next changelog run.
