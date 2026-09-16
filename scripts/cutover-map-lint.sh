#!/usr/bin/env bash
# Phase 2: only /up, /health, /horizon, /build may point at cms.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
MAP="$ROOT/proxy/cutover.map"

if [[ ! -f "$MAP" ]]; then
  echo "missing $MAP" >&2
  exit 1
fi

BAD=0
while IFS= read -r line || [[ -n "$line" ]]; do
  [[ -z "$line" || "$line" =~ ^[[:space:]]*# ]] && continue
  [[ "$line" != *cms* ]] && continue
  case "$line" in
    *"/up"*|*"health"*|*"horizon"*|*"/build"*) ;;
    *)
      echo "disallowed cms route: $line" >&2
      BAD=1
      ;;
  esac
done < "$MAP"

if [[ "$BAD" -ne 0 ]]; then
  echo "cutover-map-lint: FAIL" >&2
  exit 1
fi
echo "cutover-map-lint: OK"
