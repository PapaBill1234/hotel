# PHPRetro Phase 2 — Laravel beside XAMPP

Fresh **Laravel 13 + official React/Inertia starter kit** running next to **PapaBill1234/PHPRetro-PDO**. This phase is platform only: edge proxy, health, Redis, Horizon, scheduler, visual-contract tests. It does **not** rewrite habblets, housekeeping, Flash/SSO, sessions, or public hotel pages.

```
hotel/
  compose.yaml              # edge, cms, vite, redis, mailpit, horizon, scheduler
  compose.override.yaml     # XAMPP-beside (default)
  proxy/nginx.conf          # fail-closed strangler + /web-gallery disk alias
  proxy/cutover.map         # only /up /health /horizon /build → cms
  legacy/                   # submodule: PapaBill1234/PHPRetro-PDO
  cms/                      # laravel/react-starter-kit + overlay
```

Pin: `56f267039582acd37881b4f2198039be72ff7ae1` (see `legacy.pin`). Quackster/PHPRetro is the `upstream` remote on `legacy/` — fetch and report only. Do not merge it.

## Visual contract

`/web-gallery/*` is served from `legacy/web-gallery` via nginx `alias`. Laravel never owns that prefix.

- Do not copy `legacy/web-gallery` into `cms/public`.
- Do not import gallery CSS/JS through Vite (no hashing, no bundling).
- Hotel pages keep the 2009 URLs:
  - `/web-gallery/v2/styles/style.css`
  - `/web-gallery/static/js/common.js`
- Two PHPRetro stubs stay on Apache:
  - `/web-gallery/js/local/com.js`
  - `/web-gallery/styles/local/com.css`

`./scripts/visual-contract.sh` enforces this.

## Databases and cookies

| Name | Engine | Phase 2 rule |
| --- | --- | --- |
| `holodb` | XAMPP MySQL, usually `phpretro` | **SELECT only.** PolarIS + `phpretro_*` tables. No Laravel migrations. |
| `sys` / `hotel_cms_sys` | same MySQL server, empty schema | Laravel migrations, jobs, Horizon meta, cache fallback. |

Cookies stay split until a later phase:

- PHPRetro: `PHPSESSID` (`$_SESSION['user']` / `$_SESSION['hk_user']`)
- Laravel: `hotel_session` (Redis DB 1)

## Redis

| DB | Use | Prefix |
| --- | --- | --- |
| 0 | Cache | `hotel_` |
| 1 | Laravel sessions | cookie `hotel_session` |
| 2 | Queues + Horizon | `hotel_horizon:` |
| 3 | Scheduler heartbeat / flags | `hotel_flags:` |

## Cutover

nginx `map $uri $backend` defaults to `legacy`. Phase 2 cms routes:

- `/up`
- `/health/*` (`/health/ready`, `/health/legacy`)
- `/horizon`
- `/build/*`

Rollback: delete the line in `proxy/cutover.map`, `docker compose exec edge nginx -s reload`. XAMPP still serves the hotel.

---

## Windows + XAMPP + WSL2 (exact)

One tree. Junction XAMPP `htdocs` at `hotel/legacy`. Docker runs in WSL2. Apache stays on the Windows host.

### 0. Layout

Clone this `hotel/` folder into WSL, e.g. `~/hotel`.

```powershell
:: Administrator Command Prompt on Windows
:: Adjust drive letters to match your install.
mklink /J C:\xampp\htdocs C:\Users\YOU\hotel\legacy
```

If `htdocs` already exists, rename it first (`htdocs.bak`) then junction. Do **not** copy `web-gallery`.

### 1. Apache listens on 8080

`C:\xampp\apache\conf\httpd.conf`:

```apache
Listen 8080
```

Keep MySQL on **3306**. Start Apache + MySQL from the XAMPP panel.

Confirm the gallery CSS from Windows:

```
http://127.0.0.1:8080/web-gallery/v2/styles/style.css
```

That URL must stay identical after Docker is up (nginx aliases the same files).

### 2. Hosts

`C:\Windows\System32\drivers\etc\hosts`:

```
127.0.0.1 hotel.test
```

Browser: `http://hotel.test/` → nginx :80 → PHPRetro via XAMPP :8080.

### 3. MySQL schemas

phpMyAdmin on XAMPP (`http://127.0.0.1:8080/phpmyadmin` only if you moved it; default XAMPP phpMyAdmin is often `:80` — if Apache is only on 8080, use `http://127.0.0.1:8080/phpmyadmin`):

1. Hotel DB (often already named `phpretro`) — **do not alter PolarIS tables**.
2. Create empty database `hotel_cms_sys` (utf8mb4). Laravel migrations belong only here.

`legacy/.env` (PHPRetro, never commit):

```
DB_DSN="mysql:host=127.0.0.1;dbname=phpretro;charset=utf8mb4"
DB_USER="root"
DB_PASS=""
```

### 4. WSL2 Docker

Inside WSL, from `~/hotel`:

```bash
git submodule update --init --recursive
git -C legacy remote add upstream https://github.com/Quackster/PHPRetro.git || true

# Laravel env
cp cms/.env.example cms/.env
# set APP_KEY later via `php artisan key:generate` (entrypoint also tries)

# Composer lives in the cms container. Never composer/npm inside legacy/.
docker compose up -d --build
```

`compose.override.yaml` is auto-loaded. It points `holodb` / `sys` / `LEGACY_URL` at `host.docker.internal` (the Windows host).

First health checks:

```bash
curl -sS http://hotel.test/up
curl -sS http://hotel.test/health/ready
curl -sS http://hotel.test/health/legacy
curl -sI http://hotel.test/web-gallery/v2/styles/style.css
./scripts/visual-contract.sh
./scripts/cutover-map-lint.sh
```

Horizon UI: `http://hotel.test/horizon` (open on local only).
Mailpit UI: `http://127.0.0.1:8025`.

### 5. Daily loop

```bash
docker compose up -d
./scripts/visual-contract.sh
./scripts/cutover-map-lint.sh
./scripts/update-legacy.sh          # fetch origin + report upstream
# ./scripts/update-legacy.sh --apply  # only after typing `update-legacy`
```

Reload nginx after editing `proxy/cutover.map`:

```bash
docker compose exec edge nginx -s reload
```

### 6. Rollback

1. Restore `proxy/cutover.map` to Phase 2 lines (or delete extra cms lines).
2. `docker compose exec edge nginx -s reload`
3. Hard rollback: `docker compose stop cms horizon scheduler vite` — nginx still aliases `/web-gallery` and proxies `/` to XAMPP.

Laravel is optional on the hot path. PHPRetro-PDO remains the hotel.

---

## Git remotes on `legacy/`

| Remote | URL | Role |
| --- | --- | --- |
| `origin` | https://github.com/PapaBill1234/PHPRetro-PDO.git | Default updates. Fast-forward only after confirm. |
| `upstream` | https://github.com/Quackster/PHPRetro.git | Comparison. Fetch, report, never merge from this script. |

`scripts/update-legacy.sh` refuses to run if `legacy/` has local modifications.

Do not merge GitHub pull requests from this tree. Do not `composer install` or `npm install` inside `legacy/`.

## Out of this phase

Habblets, housekeeping, Flash/SSO, session sharing, Tailwind on public hotel pages, hashing gallery assets, merging Quackster/PHPRetro, Filament. Phase 3 is the first real page (`/papers/*` or `/help/*`) behind one new cutover line.
