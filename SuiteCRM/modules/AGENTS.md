# AGENTS.md — modules/

This directory contains all 120+ CRM modules. Each module is a self-contained unit: its own bean (ORM model), metadata (field/view definitions), language strings, views, and optional controller.

## Module anatomy

A typical module directory contains:

| File/Dir | Purpose |
|---|---|
| `<BeanName>.php` | Bean class — extends `SugarBean` (or a subclass). Defines `$module_name`, `$table_name`, and business logic methods. |
| `vardefs.php` | Field schema registered into `$dictionary['<BeanName>']`. Defines every column, its type, labels, relationships, and DB properties. This is the source of truth for the module's data model. |
| `metadata/` | View layout definitions (see below). |
| `language/` | i18n string arrays (`$mod_strings`, `$app_list_strings`). |
| `views/` | Custom view overrides (e.g. `view.detail.php`, `view.list.php`, `view.edit.php`). |
| `controller.php` | Optional custom controller extending `SugarController`. Only present when a module needs non-standard action handling. |
| `Menu.php` | Top navigation menu items for the module. |
| `Dashlets/` | Dashlet subdirectories that this module contributes to the home dashboard. |

## Metadata files (`metadata/`)

These PHP files define the layout of each view as a `$viewdefs` array:

- `detailviewdefs.php` — Detail view panel/field layout
- `editviewdefs.php` — Edit view layout
- `listviewdefs.php` — List view columns
- `searchdefs.php` / `SearchFields.php` — Search form fields
- `subpaneldefs.php` + `subpanels/` — Related-record subpanels shown on detail views
- `popupdefs.php` / `quickcreatedefs.php` — Popup selector and quick-create layouts
- `vardefs.php` at the metadata level is the same as root-level; some modules keep both

**Never edit core metadata directly.** Override via `custom/Extension/modules/<Module>/Ext/` or `custom/modules/<Module>/metadata/`.

## Module groups

### Core CRM
`Accounts`, `Contacts`, `Leads`, `Opportunities`, `Cases`, `Bugs`, `Calls`, `Meetings`, `Tasks`, `Notes`, `Emails` — the standard CRM record types, all extending `SugarBean`.

### AO* — SuiteCRM Add-ons
- `AOR_*` — Advanced OpenReports (reports, charts, scheduled reports, conditions, fields)
- `AOS_*` — Advanced OpenSales (quotes, invoices, contracts, PDF templates, products, line items)
- `AOW_*` — Advanced OpenWorkflow (workflow engine, actions, conditions, processed log)
- `AOD_*` — Advanced OpenDiscovery (full-text search index via Elasticsearch/Lucene)
- `AOK_*` — Advanced OpenKnowledge (knowledge base articles and categories)
- `AOP_*` — Advanced OpenPortal (case events/updates for customer portal)
- `AOBH_*` — Advanced OpenBusiness Hours
- `AM_*` — Project templates and task templates

### FP / jjwg — Community Add-ons
- `FP_events`, `FP_Event_Locations` — Events management
- `jjwg_Maps`, `jjwg_Markers`, `jjwg_Areas`, `jjwg_Address_Cache` — Google Maps integration

### Admin & Infrastructure
- `Administration` — System settings, repair/rebuild utilities, upgrade wizard
- `Studio` — UI-based field/layout editor (writes to `custom/`)
- `ModuleBuilder` — Creates new custom modules
- `DynamicFields` — Runtime custom field definitions (stores in `fields_meta_data` table)
- `Schedulers` / `SchedulersJobs` — Cron-driven job scheduler (`_AddJobsHere.php` registers jobs)
- `ACL`, `ACLActions`, `ACLRoles` — Field- and action-level access control
- `SecurityGroups` — Record-level group-based security
- `Roles` — User role assignments
- `Users` — Authentication, login, password management

### OAuth2
`OAuth2Clients`, `OAuth2Tokens`, `OAuth2AuthCodes`, `OAuthKeys`, `OAuthTokens` — OAuth2 server support for the V8 API and external integrations.

### Campaigns & Marketing
`Campaigns`, `CampaignLog`, `CampaignTrackers`, `EmailMarketing`, `EmailMan`, `ProspectLists`, `Prospects` — Full campaign wizard, bounce handling, web-to-lead forms, newsletter subscriptions.

### Calendar & Sync
`Calendar`, `CalendarAccount`, `iCals`, `vCals`, `Reminders`, `Reminders_Invitees` — Calendar rendering and iCal/CalDAV sync.

### Surveys
`Surveys`, `SurveyQuestions`, `SurveyQuestionOptions`, `SurveyResponses`, `SurveyQuestionResponses` — Survey builder and response capture.

## Adding or modifying a module

1. **Add a field**: Edit `vardefs.php` → add to `$dictionary['<Bean>']['fields']`. Then run **Admin → Quick Repair and Rebuild** (`Administration/QuickRepairAndRebuild.php`) to sync DB schema.
2. **Change a view layout**: Edit the relevant `metadata/*defs.php`. Prefer creating `custom/modules/<Module>/metadata/<file>` over editing core.
3. **Add business logic**: Create or edit a logic hook file at `custom/modules/<Module>/logic_hooks.php`. Hook classes can live anywhere autoloaded; by convention they go in the same directory.
4. **Add a scheduled job**: Register in `modules/Schedulers/_AddJobsHere.php` (or its `custom/` extension).
5. **Custom controller action**: Add `action_<name>()` to the module's `controller.php` (or create `custom/modules/<Module>/controller.php`).

## Key conventions

- The bean class name must match the module folder name (singular). The DB table name is typically the plural snake_case.
- `$this->bean_name` inside a module refers to the class name; `$this->module_name` is the folder name — they are often identical but not always (e.g. `Prospects` module, `Prospect` bean).
- Views check `sugarEntry` at the top — never include module files directly from outside the MVC lifecycle.
- Module language strings live in `language/en_us.lang.php`; always use `$mod_strings['LBL_*']` rather than hardcoded text.
