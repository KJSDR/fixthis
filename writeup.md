# SuiteCRM — Personal Notes

## What is it?

SuiteCRM is an open-source CRM (Customer Relationship Management) web app. A CRM is essentially a database with a rich UI for managing business relationships: customers, leads, sales pipelines, emails, meetings, tasks, and so on. SuiteCRM is the open-source fork of SugarCRM, built on PHP + MySQL + Apache.

You access it through a browser. Everything is organized into **modules** — each module is a record type (Contacts, Accounts, Opportunities, etc.) with its own list view, detail view, edit form, and search.

---

## Core things the app does

| Area | What it is |
|---|---|
| **Accounts & Contacts** | Companies and people. The bread and butter of the CRM. |
| **Leads & Opportunities** | Prospective customers and deals in the sales pipeline. |
| **Cases & Bugs** | Customer support tickets and issue tracking. |
| **Calls, Meetings, Tasks, Notes** | Activity tracking — what happened with who and when. |
| **Emails** | Send/receive email, attach to records. |
| **Campaigns** | Email marketing campaigns, newsletters, web-to-lead forms. |
| **Reports (AOR_*)** | Build custom reports and charts from any module's data. |
| **Quotes & Invoices (AOS_*)** | Full sales document workflow with line items and PDF generation. |
| **Workflows (AOW_*)** | Automation rules — "when X happens, do Y". |
| **Surveys** | Build and send surveys, collect responses. |
| **Calendar** | Unified view of calls/meetings, syncs via iCal. |
| **Knowledge Base (AOK_*)** | Internal wiki / article store. |
| **Maps (jjwg_*)** | Google Maps integration for geocoding accounts/contacts. |

---

## How the code is structured (simplified)

```
SuiteCRM/
├── index.php            ← every page request starts here
├── modules/             ← one folder per feature area (~120 modules)
├── include/             ← shared framework code (MVC engine, utilities)
├── data/                ← ORM base class (SugarBean) and relationships
├── Api/V8/              ← REST API (modern, JSON:API format)
├── custom/              ← YOUR customizations go here (mirrors core structure)
├── themes/              ← frontend CSS/templates
└── tests/               ← unit + acceptance tests
```

Each module in `modules/` follows the same pattern:
- `<Name>.php` — the model (fields, DB table, business logic)
- `vardefs.php` — declares every field and its type
- `metadata/` — controls what appears on list/detail/edit screens
- `views/` — custom rendering logic
- `language/` — all display strings (no hardcoded labels in templates)

---

## What you'll touch most

**To change what fields exist on a record:** `modules/<Module>/vardefs.php`

**To change what shows on a form or list:** `modules/<Module>/metadata/*defs.php`
- `editviewdefs.php` = edit form layout
- `detailviewdefs.php` = record detail page
- `listviewdefs.php` = column layout in list views

**To add business logic (hooks):** `custom/modules/<Module>/logic_hooks.php`
Hooks fire at bean lifecycle events: before/after save, before/after delete, etc.

**To add a custom action:** `modules/<Module>/controller.php` → add `action_<name>()`

**To make any change safely (without breaking upgrades):** put your file in `custom/` mirroring the path of the core file. SuiteCRM always checks `custom/` first.

---

## The data model in one sentence

Everything is a **Bean** — a PHP object that maps to a DB table. `SugarBean` (`data/SugarBean.php`) is the base class with `save()`, `retrieve()`, `mark_deleted()`, etc. Every module's main PHP class extends it.

---

## REST API

The modern API lives at `/api/v8/` and follows the JSON:API spec. It uses OAuth2 for auth. You can CRUD any bean through it. The legacy SOAP/REST APIs (`/service/v4_1/`) still exist but are obsolete.

---

## After making code changes

Run **Admin → Quick Repair and Rebuild** in the browser UI. This regenerates caches, syncs the DB schema to vardefs, and rebuilds JS/CSS. Most changes won't show up until you do this.

---

## Assignment Notes

### V1.0 — Exploration & Understanding

**Map the codebase architecture using AI tools. Save exploration logs or summaries.**
Used Claude Code to walk through the full directory tree — top-level layout, then drilling into `include/MVC/`, `data/`, `modules/`, `Api/V8/`, `custom/`, and `tests/`. Read key files (`index.php`, `SugarApplication.php`, `SugarBean.php`, `composer.json`, `codeception.dist.yml`) to understand the request lifecycle, ORM pattern, and test setup. Findings were captured directly into CLAUDE.md and this writeup.

**Create CLAUDE.md based on what you learned — tech stack, patterns, conventions, gotchas.**
Created `CLAUDE.md` at the repo root (outside `SuiteCRM/` so it covers the whole project). It documents: the tech stack (PHP 8.1+, MySQL, Apache, Smarty), all dev commands (composer, phpunit, codecept, phpcs), the full request lifecycle, MVC architecture, module structure, the `custom/` override system, the V8 REST API, PSR-4 autoloading, logic hooks, and the `sugarEntry` guard gotcha.

**Create at least 1 AGENTS.md in a key subdirectory with scoped instructions for that area.**
Created `SuiteCRM/modules/AGENTS.md`. Chose `modules/` because it's the directory agents will touch most often. It covers: the standard module anatomy (every file and what it does), all metadata file types and their purpose, module groups organized by domain (core CRM, AO* add-ons, admin/infrastructure, OAuth2, etc.), step-by-step instructions for the most common modification tasks, and key naming conventions.

---

### V1.1 — Rules & Context Tuning

**Write at least 3 rules with glob or path scoping that enforce the project's existing conventions.**
Added a `## Rules` section to `CLAUDE.md` with 3 scoped rules derived from patterns observed consistently across all 120+ modules:
1. `SuiteCRM/modules/**/*.php` + `include/**/*.php` — every PHP file must open with the `sugarEntry` guard (security, prevents direct HTTP access).
2. `SuiteCRM/modules/*/metadata/*.php` — never edit core metadata; all layout changes go in the `custom/` mirror path (upgrade safety).
3. `SuiteCRM/modules/*/language/*.php` — language files are pure `$mod_strings` / `$app_list_strings` arrays only, no logic or HTML (required by the i18n loader).

**Test your rules by running a small task and observing agent behavior.**
Ran one task per rule:
- Rule 1: Created `modules/Home/TestHelper.php` — guard was included on line 2 before any code. ✓
- Rule 2: Added a `preferred_name` field to the Contacts edit view — wrote to `custom/modules/Contacts/metadata/editviewdefs.php`, core file untouched. ✓
- Rule 3: Added `LBL_PREFERRED_NAME` label — written as a plain `$mod_strings` assignment in `custom/modules/Contacts/language/en_us.lang.php`, no logic. ✓
All three rules were followed naturally. The test file from Rule 1 was cleaned up; the Rule 2 and 3 files are valid customizations and kept.

**Refine your CLAUDE.md or rules based on output quality. Document what you changed and why in writeup.md.**
Rules held cleanly on first pass — no refinement needed yet. One observation: Rule 2's scope (`modules/*/metadata/`) technically only catches core files, but the underlying principle (always use `custom/`) is broader. May expand this rule's wording in a future pass to also cover `modules/*/vardefs.php` direct edits.