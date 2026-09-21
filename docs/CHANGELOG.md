# Changelog

All notable changes to the Chemical X IDE WordPress Plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [26.9.11] - 2026-09-11

### Added
- Initial release of Chemical X IDE WordPress companion plugin router.
- Clean endpoint rewrites deploying slugs `/ide` and `/chemical-x-ide` with custom slug support.
- Development reverse-proxy forwarding requests to local Vite/Nuxt port `8095`.
- Production dist fallback loader injecting `window.wpApiSettings` with user capability metadata.
- REST API controller (`chemical-x/v1`) providing `/health` and authenticated `/workspace` persistence endpoints.
- Integration hook registering Chemical X IDE with the Event Horizon / YouMeOS Spark Registry.
