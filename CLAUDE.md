# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

SuiteCRM 7.15.1 is an open-source enterprise CRM built on PHP 8.1+, forked from SugarCRM Community Edition. It runs on a LAMP stack (Apache, PHP, MySQL/MariaDB) and uses Smarty for templating.

## Commands

### Dependencies
```bash
composer install
```

### Code Style (PSR-2)
```bash
vendor/bin/phpcs --standard=phpcs.xml <file>
vendor/bin/php-cs-fixer fix <file>
```

### Unit Tests (PHPUnit)
```bash
# All unit tests
cd tests && ../vendor/bin/phpunit

# Single test file
cd tests && ../vendor/bin/phpunit unit/phpunit/path/to/TestFile.php

# Single test method
cd tests && ../vendor/bin/phpunit --filter testMethodName unit/phpunit/path/to/TestFile.php
```

### Acceptance Tests (Codeception)
```bash
# Requires a running SuiteCRM instance and .env.test configured
vendor/bin/codecept run acceptance
vendor/bin/codecept run api
```

### Install
```bash
php install.php  # CLI installer (set up DB credentials first in config.php)
```

## Architecture

### Request Lifecycle
Every HTTP request enters through `index.php`, which bootstraps via `include/entryPoint.php`, then hands off to `SugarApplication` (`include/MVC/SugarApplication.php`). The application uses `ControllerFactory` and `ViewFactory` to dispatch to the correct module controller and view. URL parameters `module` and `action` determine routing (e.g., `?module=Contacts&action=index`).

### MVC Pattern
- **Controllers**: `include/MVC/Controller/SugarController.php` is the base. Module-specific controllers live in `modules/<ModuleName>/controller.php` or `modules/<ModuleName>/views/`.
- **Views**: `include/MVC/View/SugarView.php` is the base. Views render Smarty `.tpl` templates from `include/MVC/View/tpls/` and module-level `modules/<ModuleName>/metadata/`.
- **Models (Beans)**: `data/SugarBean.php` is the ORM base class for all CRM entities. Every module's primary class extends `SugarBean`. Naming: bean class = singular (e.g. `Contact`), DB table = plural (e.g. `contacts`). `data/BeanFactory.php` is the factory for instantiation.

### Module Structure
`modules/` contains 120+ modules. Each module typically has:
- `<BeanName>.php` — the bean class extending `SugarBean`
- `metadata/` — field definitions, listview/detailview/editview layouts
- `language/` — i18n strings
- `controller.php` — optional custom controller
- `views/` — optional custom views

### Customization Layer
`custom/` mirrors the core directory structure. Files placed in `custom/modules/<ModuleName>/` or `custom/Extension/` override or extend core behavior without modifying core files. This is the correct place for all customizations.

### REST API (V8)
The modern JSON:API-compliant REST API lives in `Api/V8/`. Entry point is `Api/V8/app.php` using Slim 3. OAuth2 authentication is handled by `Api/V8/OAuth2/`. Legacy SOAP/REST APIs exist under `service/` (v2–v4_1) but are deprecated.

### Key Infrastructure
- **PSR-4 Autoloading**: `SuiteCRM\` namespace maps to `lib/` and `include/`; `SuiteCRM\Modules\` maps to `modules/`; `SuiteCRM\Custom\` maps to `custom/lib/`.
- **Relationships**: `data/Relationships/` manages many-to-many and one-to-many bean relationships via a metadata-driven system.
- **Hooks**: Logic hooks (`modules/<Module>/logic_hooks.php`) allow event-driven code injection at bean lifecycle points (before/after save, delete, etc.).
- **Entry Point Guard**: All PHP files check `if (!defined('sugarEntry') || !sugarEntry) { die('Not A Valid Entry Point'); }` at the top. Do not bypass this.

### Testing Layout
- `tests/unit/phpunit/` — PHPUnit unit tests, mirroring the source structure
- `tests/acceptance/` — Codeception browser-based acceptance tests
- `tests/api/` — Codeception API tests for V8 REST endpoints
- Test config: `tests/.env.test` (copied from `.env.test.dist` for acceptance/API tests)
