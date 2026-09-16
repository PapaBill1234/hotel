#!/usr/bin/env bash
# Fetch PapaBill1234/PHPRetro-PDO (origin). Report Quackster/PHPRetro (upstream).
# Never merge upstream. Fast-forward origin only after typing `update-legacy`.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
LEGACY="$ROOT/legacy"
PIN_FILE="$ROOT/legacy.pin"
APPLY=0

if [[ "${1:-}" == "--apply" ]]; then
  APPLY=1
elif [[ -n "${1:-}" ]]; then
  echo "Usage: $0 [--apply]" >&2
  exit 2
fi

if [[ ! -d "$LEGACY/.git" ]]; then
  echo "legacy/ is not a git checkout. From $ROOT run:" >&2
  echo "  git submodule update --init --recursive" >&2
  echo "  git -C legacy remote add upstream https://github.com/Quackster/PHPRetro.git" >&2
  exit 1
fi

cd "$LEGACY"

if ! git remote get-url origin >/dev/null 2>&1; then
  git remote add origin https://github.com/PapaBill1234/PHPRetro-PDO.git
fi
if ! git remote get-url upstream >/dev/null 2>&1; then
  git remote add upstream https://github.com/Quackster/PHPRetro.git
fi

ORIGIN_URL="$(git remote get-url origin)"
if [[ "$ORIGIN_URL" != *PapaBill1234/PHPRetro-PDO* ]]; then
  echo "Refusing: origin is $ORIGIN_URL (expected PapaBill1234/PHPRetro-PDO)" >&2
  exit 1
fi

echo "== remotes =="
git remote -v
echo

echo "== fetch origin (PapaBill1234/PHPRetro-PDO) =="
git fetch origin --prune
echo

ORIGIN_HEAD="$(git symbolic-ref --quiet refs/remotes/origin/HEAD 2>/dev/null || true)"
if [[ -z "$ORIGIN_HEAD" ]]; then
  if git show-ref --verify --quiet refs/remotes/origin/master; then
    ORIGIN_REF="origin/master"
  else
    ORIGIN_REF="origin/main"
  fi
else
  ORIGIN_REF="${ORIGIN_HEAD#refs/remotes/}"
fi

PIN="$(tr -d '[:space:]' < "$PIN_FILE" 2>/dev/null || true)"
CURRENT="$(git rev-parse HEAD)"
TARGET="$(git rev-parse "$ORIGIN_REF")"

echo "pinned:     ${PIN:-none}"
echo "checked out:$CURRENT"
echo "origin:     $TARGET  ($ORIGIN_REF)"
echo

echo "== fetch upstream (Quackster/PHPRetro) — report only, never apply =="
git fetch upstream --prune
echo
echo "upstream/master commits not in origin:"
if git rev-parse --verify --quiet upstream/master >/dev/null; then
  git log --oneline --decorate "$ORIGIN_REF"..upstream/master | head -n 40 || true
  COUNT="$(git rev-list --count "$ORIGIN_REF"..upstream/master 2>/dev/null || echo 0)"
  echo "(${COUNT} commit(s) — review before any cherry-pick. This script will not merge them.)"
else
  echo "(upstream/master not fetched)"
fi
echo
if git rev-parse --verify --quiet upstream/dev/php8 >/dev/null; then
  echo "upstream/dev/php8 tip: $(git rev-parse --short upstream/dev/php8)"
fi
echo

if [[ "$APPLY" -eq 0 ]]; then
  echo "Dry run. To fast-forward legacy/ to $ORIGIN_REF, run:"
  echo "  $0 --apply"
  echo "You will be asked to type: update-legacy"
  exit 0
fi

if [[ -n "$(git status --porcelain --untracked-files=no)" ]]; then
  echo "Refusing --apply: local modifications in legacy/. Commit, stash, or discard them first." >&2
  git status --short
  exit 1
fi

if [[ "$CURRENT" == "$TARGET" ]]; then
  echo "Already at $ORIGIN_REF ($TARGET). Pin file updated."
  printf '%s\n' "$TARGET" > "$PIN_FILE"
  exit 0
fi

echo "About to fast-forward HEAD -> $ORIGIN_REF ($TARGET)."
echo "This never merges Quackster/PHPRetro."
printf "Type update-legacy to continue: "
read -r CONFIRM
if [[ "$CONFIRM" != "update-legacy" ]]; then
  echo "Aborted."
  exit 1
fi

git merge --ff-only "$TARGET"
NEW="$(git rev-parse HEAD)"
printf '%s\n' "$NEW" > "$PIN_FILE"
echo "legacy/ is now $NEW"
echo "pin written to $PIN_FILE"
