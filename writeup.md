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
