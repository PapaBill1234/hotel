#!/usr/bin/env bash
# Copy cms-overlay/ onto the official Laravel React starter kit in cms/.
# Safe to re-run. Does not touch legacy/. Never copies web-gallery.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OVERLAY="$ROOT/cms-overlay"
CMS="$ROOT/cms"

if [[ ! -d "$OVERLAY" ]]; then
  echo "missing $OVERLAY" >&2
  exit 1
fi
if [[ ! -f "$CMS/artisan" ]]; then
  echo "cms/ is not a Laravel app (missing artisan)" >&2
  exit 1
fi

# No rsync in this environment — walk the overlay tree.
find "$OVERLAY" -type f | while IFS= read -r src; do
  rel="${src#"$OVERLAY"/}"
  case "$rel" in
    public/web-gallery|public/web-gallery/*)
      echo "refusing to copy $rel" >&2
      exit 1
      ;;
  esac
  dest="$CMS/$rel"
  mkdir -p "$(dirname "$dest")"
  cp -a "$src" "$dest"
done

if [[ -f "$CMS/routes/web.php" ]] && ! grep -q "health.php" "$CMS/routes/web.php"; then
  printf "\nrequire __DIR__.'/health.php';\n" >> "$CMS/routes/web.php"
fi

if [[ -f "$CMS/composer.json" ]] && ! grep -q 'laravel/horizon' "$CMS/composer.json"; then
  python3 - "$CMS/composer.json" <<'PY'
import json, sys
path = sys.argv[1]
data = json.load(open(path))
data.setdefault("require", {})["laravel/horizon"] = "^5.39"
with open(path, "w") as fh:
    json.dump(data, fh, indent=4)
    fh.write("\n")
PY
fi

if [[ ! -f "$CMS/.env" && -f "$CMS/.env.example" ]]; then
  cp "$CMS/.env.example" "$CMS/.env"
fi

echo "overlay applied onto cms/"
