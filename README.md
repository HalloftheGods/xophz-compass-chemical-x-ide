# Chemical X IDE WordPress Plugin

WordPress backend router, dev server reverse-proxy, and REST bridge for the Chemical X IDE SPA (`apps/my-chemical-x-ide`).

## Slugs Deployed
- `/ide` (Primary SPA route)
- `/chemical-x-ide` (Secondary SPA route)

## Architecture
- Development Mode: Reverse-proxies to `http://localhost:8095` and injects `window.wpApiSettings`.
- Production Mode: Loads compiled assets from `public/dist/index.html`.
- REST API: Namespace `chemical-x/v1` for workspace synchronization and health telemetry.
