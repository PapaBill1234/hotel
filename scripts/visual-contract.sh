#!/usr/bin/env bash
# Fail if /web-gallery is copied, hashed, or imported through Vite.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
CMS="$ROOT/cms"
FAIL=0

say() { printf '%s\n' "$*"; }
fail() { say "FAIL: $*"; FAIL=1; }
pass() { say "PASS: $*"; }

if [[ -e "$CMS/public/web-gallery" ]]; then
  fail "cms/public/web-gallery exists — never copy the gallery into Laravel public"
else
  pass "cms/public/web-gallery is absent"
fi

if [[ -d "$CMS/public/build" ]]; then
  if grep -R --include='*' -l 'web-gallery' "$CMS/public/build" >/dev/null 2>&1; then
    fail "hashed Vite build under cms/public/build mentions web-gallery"
  else
    pass "cms/public/build does not mention web-gallery"
  fi
else
  pass "no cms/public/build yet (ok in Phase 2 before first npm run build)"
fi

# Vite must not import or list gallery files. Comments mentioning the ban are ok.
if [[ -f "$CMS/vite.config.ts" ]]; then
  if grep -E "^[[:space:]]*[^/].*web-gallery" "$CMS/vite.config.ts" \
      | grep -Ev '^[[:space:]]*(//|\*)' >/dev/null 2>&1; then
    fail "vite.config.ts references web-gallery outside a comment"
  else
    pass "vite.config.ts does not import web-gallery"
  fi
else
  fail "cms/vite.config.ts missing"
fi

# Resources must not import gallery assets. Exact URL strings in HTML/JS hrefs are required.
if grep -R --include='*.{ts,tsx,js,jsx,css}' -n "from ['\"].*web-gallery" "$CMS/resources" >/dev/null 2>&1; then
  fail "resources import a web-gallery module path"
else
  pass "no JS/CSS import of web-gallery"
fi

SKIN="$CMS/resources/js/layouts/legacy-skin.tsx"
if [[ -f "$SKIN" ]]; then
  grep -q '/web-gallery/v2/styles/style.css' "$SKIN" \
    && pass "legacy-skin links /web-gallery/v2/styles/style.css" \
    || fail "legacy-skin missing exact /web-gallery/v2/styles/style.css"
  grep -q '/web-gallery/static/js/common.js' "$SKIN" \
    && pass "legacy-skin links /web-gallery/static/js/common.js" \
    || fail "legacy-skin missing exact /web-gallery/static/js/common.js"
else
  fail "cms/resources/js/layouts/legacy-skin.tsx missing"
fi

NGINX="$ROOT/proxy/nginx.conf"
if grep -q 'alias /var/www/web-gallery/' "$NGINX"; then
  pass "nginx aliases /web-gallery/ from disk"
else
  fail "nginx.conf missing alias /var/www/web-gallery/"
fi

MAP="$ROOT/proxy/cutover.map"
if grep -vE '^[[:space:]]*(#|$)' "$MAP" | grep -E 'habblet|housekeeping' >/dev/null 2>&1; then
  fail "cutover.map contains a non-Phase-2 cms route"
else
  pass "cutover.map is Phase 2 only (/up /health /horizon /build)"
fi

if [[ -f "$ROOT/legacy/web-gallery/v2/styles/style.css" ]]; then
  pass "legacy submodule has web-gallery/v2/styles/style.css"
else
  fail "legacy/web-gallery/v2/styles/style.css missing — init the submodule"
fi

if [[ "$FAIL" -ne 0 ]]; then
  say "visual-contract: FAIL"
  exit 1
fi
say "visual-contract: OK"
