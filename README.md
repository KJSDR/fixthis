# ACS4220 — SuiteCRM Lab

This repo contains a local copy of **SuiteCRM 7.15.1**, an open-source PHP-based CRM application, used as the subject for exploration and modifications in this course.

## What is SuiteCRM?

SuiteCRM is a web-based Customer Relationship Management system. It lets businesses track contacts, accounts, leads, deals, emails, tasks, and more through a browser UI. It runs on a standard LAMP stack (Linux, Apache, MySQL, PHP 8.1+).

## Repo structure

```
fixthis/
├── SuiteCRM/       ← the full application source
├── CLAUDE.md       ← guidance for Claude Code AI assistant
└── writeup.md      ← personal notes and exploration guide
```

## SuiteCRM architecture (high level)

- **Entry point**: every request hits `SuiteCRM/index.php`, which boots the MVC framework and routes to the right module + action via `?module=X&action=Y`.
- **Modules** (`SuiteCRM/modules/`): ~120 feature areas (Contacts, Accounts, Opportunities, etc.), each with its own model, views, and metadata.
- **Bean / ORM** (`SuiteCRM/data/SugarBean.php`): base class for all data models. Every module's main class extends it.
- **Customization** (`SuiteCRM/custom/`): mirrors the core directory structure. Always put changes here to survive upgrades.
- **REST API** (`SuiteCRM/Api/V8/`): JSON:API-compliant, OAuth2-authenticated.

## Requirements

- PHP 8.1–8.4
- MySQL or MariaDB
- Apache (recommended)

## Key dev step

After any code change, run **Admin → Quick Repair and Rebuild** in the browser to flush caches and sync the DB schema.
