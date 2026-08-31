# FleetDesk — Build Brief for Claude Code

## What this project is

FleetDesk is a delivery/job management system for a fuel & waste logistics
company (modeled loosely on Crown Oil / Wheeldon Brothers Waste). Dispatchers
create delivery jobs, assign drivers, and track them through a lifecycle.
Drivers' status changes broadcast live to a dashboard over WebSockets.
Completed/scheduled jobs sync to a Microsoft 365 calendar via Microsoft Graph.

This is a **portfolio project** built to demonstrate: PHP 8+/Laravel, Vue 3
Composition API, TypeScript, GraphQL, MySQL, PHPUnit/TDD, Docker, GitHub
Actions CI/CD, Soketi/WebSockets, and Microsoft Graph integration — for a
mid-level PHP developer job application. Code quality, test coverage, and
clean git history matter as much as features.

## Tech stack (do not substitute without asking)

- **Backend**: PHP 8.3+, Laravel 11 (or latest stable), Sanctum for auth
- **GraphQL**: `nuwave/lighthouse` (schema-first GraphQL for Laravel)
- **DB**: MySQL 8.4
- **Frontend**: Vue 3 `<script setup lang="ts">`, Vite, Pinia, TypeScript strict mode
- **GraphQL client**: `graphql-request` or `urql` with generated types (`graphql-codegen`)
- **Realtime**: Laravel Broadcasting + Soketi (Pusher-protocol compatible), Laravel Echo on frontend
- **Testing**: PHPUnit (feature tests, TDD-first), Pest is acceptable if preferred — ask before switching
- **Containerization**: Docker Compose — app (php-fpm + nginx or Sail), mysql, soketi, node (for asset build)
- **CI/CD**: GitHub Actions — lint (Pint/ESLint), PHPUnit, Vue type-check (`vue-tsc`), frontend build, all on PR
- **External integration**: Microsoft Graph API (OAuth 2.0 client credentials or auth code flow) for calendar events

## Domain model (v1)

- `User` — roles: `dispatcher`, `driver`, `manager` (simple enum column is fine, no need for full RBAC package)
- `Customer` — name, address, contact info
- `Driver` — linked to a `User`, current status (`available`, `on_job`, `off_duty`)
- `Job` — customer, driver (nullable until assigned), status enum
  (`pending` → `assigned` → `in_transit` → `completed` → `cancelled`),
  scheduled_at, completed_at, notes
- `JobStatusUpdated` event — fired on every status transition, broadcasts to a
  `jobs` channel, triggers Microsoft Graph calendar sync on `completed`/`assigned`

Keep this to four core models. Do not add scope beyond this in v1 — resist
the urge to build customer invoicing, payments, etc. Depth over breadth.

## Build order — work through phases sequentially, one PR-sized commit set per phase

### Phase 0 — Scaffold

- `laravel new fleetdesk`, install Sanctum
- `docker-compose.yml`: app, mysql, soketi, node build container
- GitHub Actions workflow that runs on every push: `composer install`, `php artisan test` (even with zero tests, get the pipeline green first)
- Confirm `docker-compose up` boots cleanly before moving on

### Phase 1 — Domain + TDD

- Migrations + model factories for User, Customer, Driver, Job
- **Write PHPUnit feature tests first** for: creating a job, assigning a driver, transitioning job status (including invalid transition rejection), listing jobs with filters
- Only then implement the controllers/services to make tests pass
- Commit tests and implementation as separate commits so the TDD flow is visible in history

### Phase 2 — GraphQL API

- Install Lighthouse, define schema: `Job`, `Driver`, `Customer` types; `jobs(status: JobStatus, driverId: ID): [Job!]!` query; `assignDriver`, `updateJobStatus` mutations
- Feature tests for GraphQL queries/mutations (Lighthouse has test helpers — use them)
- Keep a couple of REST endpoints too (e.g. auth) to show both are understood, not GraphQL-only

### Phase 3 — Vue 3 + TypeScript frontend

- Vite + Vue 3 + TS strict mode, Pinia store for jobs/drivers
- Typed GraphQL client with codegen so query results are typed, not `any`
- Job board view: list/filter jobs, assign driver, change status
- Component structure: keep components small, typed props, no `any`

### Phase 4 — Realtime

- Laravel private broadcasting channel `jobs`, broadcast `JobStatusUpdated`
- Soketi running in Docker, Echo configured on frontend
- Dashboard updates live without refresh when a job status changes — this is the key demo moment, make sure it's easy to show in a GIF/video

### Phase 5 — Microsoft Graph integration

- Azure AD app registration (I will set this up separately — flag if you need placeholder env vars)
- On job `assigned` or `completed`, create/update a calendar event via Graph API
- Wrap this in a service class with an interface so it's mockable in tests (don't hit real Graph API in CI)
- Document setup steps in README since this needs real credentials to run live

### Phase 6 — Polish

- Seed data + a demo script/command (`php artisan demo:seed`)
- README: architecture diagram (can be ASCII or mermaid), setup instructions, screenshots/GIF of live dashboard, explicit mapping of README section → job requirement
- CI badge in README
- Note in README that this was built with Claude Code, briefly describing how

## Conventions

- Conventional commits (`feat:`, `fix:`, `test:`, `refactor:`, `chore:`)
- One logical change per commit; don't squash test+implementation into one commit for TDD-demonstrating slices
- PHP: follow Laravel Pint defaults
- TypeScript: strict mode on, no implicit `any`
- Every new feature gets a feature test before or alongside implementation — do not skip tests to move faster
- Ask before adding new major dependencies not listed in the stack above

## Definition of done for the whole project

- `docker-compose up` gives a fully working app with one command
- CI is green on main
- PHPUnit + Vue type-check both pass in CI
- Live WebSocket update is demonstrably working (screen recording in README)
- README clearly maps each job-post requirement to where it's demonstrated in the repo
