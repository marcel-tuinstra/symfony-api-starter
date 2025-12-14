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

- To finalize a release, see `docs/release.md` for the manual flow (finalize changelog, merge `develop` → `main`, tag, push).

## How it works

- Scans commit subjects and groups them by type (`feat`, `fix`, `docs`, `chore`, etc.).
- Generates an `[Unreleased]` section with the detected changes and keeps existing release sections intact.
- Deduplicates repeated commit messages to keep the log tidy.

## Tips

- Keep using [Conventional Commits](https://www.conventionalcommits.org/en/v1.0.0/) so grouping stays accurate.
- Create lightweight tags (`git tag v0.1.0 && git push --tags`) to scope the next changelog run.
