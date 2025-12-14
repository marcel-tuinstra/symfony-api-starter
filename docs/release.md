# Release Flow

Manual steps for cutting a release (tags are created on `main`).

## Preconditions
- `develop` is green (CI/tests).
- Working tree clean (no uncommitted changes).
- Xdebug coverage enabled if you need a coverage run.

## Steps
1) From `develop`, refresh changelog and finalize `[Unreleased]` into the release version:
   ```bash
   composer changelog
   php bin/console app:release:finalize --version vX.Y.Z
   ```
2) Commit and push the changelog and any release notes to `develop`.
3) Fast-forward `main` and tag:
   ```bash
   git checkout main
   git merge --ff-only develop
   # (optional) Update version in .env.dist/.env.local for runtime display, commit if needed
   git tag -a vX.Y.Z -m "Release vX.Y.Z"
   git push origin main vX.Y.Z
   git checkout develop
   ```

## Notes
- If the tag already exists, delete/retag as needed before pushing.
- Keep `CHANGELOG.md` curated; `composer changelog` can be re-run before finalize if needed.
- Ensure secrets for deploy workflows exist (otherwise deploy job will skip).
