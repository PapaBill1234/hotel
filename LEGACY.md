# legacy/ — PapaBill1234/PHPRetro-PDO

This directory is a **git submodule** of https://github.com/PapaBill1234/PHPRetro-PDO.

Pinned commit: see `legacy.pin` (currently `56f267039582acd37881b4f2198039be72ff7ae1`).

PHPRetro 4.0.10.85. Treat the tree as **read-only** in Phase 2.

## Remotes

```
origin    https://github.com/PapaBill1234/PHPRetro-PDO.git   (default updates)
upstream  https://github.com/Quackster/PHPRetro.git          (comparison only)
```

Default updates come from `origin`. Fetch `upstream` for a report; do not merge, rebase, or cherry-pick it without a separate reviewed change.

```bash
./scripts/update-legacy.sh           # fetch + report
./scripts/update-legacy.sh --apply   # fast-forward origin after typing update-legacy
```

The apply path is fast-forward only and aborts if `legacy/` is dirty.

## Do not

- Run `composer install` / `npm install` / `vite` here
- Copy `web-gallery/` into `cms/public`
- Rewrite habblets, housekeeping, Flash/SSO, sessions, or public pages
- Merge GitHub pull requests into this checkout from Phase 2 tooling
