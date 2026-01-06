# SIM LPPM ITSNU - Agent Guidelines

## 1. Project Summary & Stack
Research & Community Service Management System for Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan.
- **Stack:** PHP 8.4, Laravel 12, Livewire v3, Flux UI (Free), Tailwind v4, Tabler + Bootstrap 5, Pest v4, Pint.
- **Key Models:** Polymorphic `Proposal` (morphs to `Research` or `CommunityService`), `User` (Spatie roles), `ProposalReviewer`.
- **Architecture:** Controller-less (mostly Livewire), Form Objects for state, Traits for composable behavior, Abstract classes for shared logic.

## 2. Core Commands
- **Lint & Format:** `vendor/bin/pint --dirty` (REQUIRED before commit)
- **Run Tests:** `php artisan test --filter=name` (Pest v4)
- **Asset Build:** `bun run build` (Builds Vite assets)
- **Queue:** `php artisan queue:listen` (Handles notifications)
- **Logs:** `php artisan pail` (Real-time log monitoring)
- **Cache:** `php artisan optimize:clear`

## 3. Code Style & PHP Guidelines
- **Modern PHP:** Use constructor property promotion, explicit return types, and parameter type hints.
- **Control Structures:** Always use curly braces `{}` even for single-line statements.
- **Early Returns:** Prefer guard clauses: `if (!$can) abort(403);` instead of nested if-blocks.
- **Naming:**
    - Classes/Enums: `PascalCase`
    - Methods/Variables: `camelCase`
    - DB Fields/Tables: `snake_case`
    - Enum Keys: `TitleCase` (e.g., `ProposalStatus::Approved`)
- **Config:** Use `config('app.name')`, NEVER use `env()` outside of config files.
- **Models:** Use `protected function casts(): array` method instead of `$casts` property.

## 4. Livewire v3 Patterns
- **Root Element:** Every component MUST have exactly one root HTML element.
- **Performance:** Use `wire:key` in loops to prevent DOM diffing issues.
- **Binding:** `wire:model.live` for real-time; `wire:model` is deferred by default.
- **Form Objects:** Move complex validation/state to `Livewire\Form` classes in `app/Livewire/Forms/`.
- **Events:** Use `$this->dispatch('event-name')` (Livewire v3) not `emit()`.
- **Computed:** Use `#[Computed]` for reactive/cached data (e.g., `$this->proposals`).

## 5. Database & Authorization
- **UUIDs:** Use `HasUuids` trait for primary keys on relevant models (Proposal, etc.).
- **N+1 Prevention:** REQUIRED eager loading `with(['relation'])` for all collections.
- **Eloquent Only:** Avoid `DB::` facade; use `Model::query()`. Use relationships for joins.
- **Authorization:** Use Spatie `hasRole()`/`can()`. Use Policies for complex logic.
- **Scoping:** `Dekan` role is faculty-scoped; `Dosen` is own-proposal scoped.

## 6. Business Workflow & Transitions
- **Team:** All invited members must `accepted` before submission. Rejection -> `NEED_ASSIGNMENT`.
- **Approval Flow:** `Dosen` (Draft) -> `Dekan` (Approve) -> `Kepala LPPM` (Initial) -> `Reviewers` -> `Kepala LPPM` (Final).
- **Status Enum:** `ProposalStatus` contains logic for `canTransitionTo()`, `label()`, and `color()`.
- **Reviews:** Proposal becomes `REVIEWED` only when ALL assigned reviewers complete their evaluations.

## 7. UI & Frontend Conventions
- **Language:** Indonesian for UI labels/messages; English for code and database identifiers.
- **Components:** Check `resources/views/components/tabler/` and `flux:*` before writing custom HTML.
- **Layouts:** Use `x-slot:title`, `x-slot:pageTitle`, `x-slot:pageActions` for standard pages.
- **Tom Select:** Add `x-data="tomSelect"` to `<select>` elements for searchable dropdowns.

## 8. 7 System Roles (database/seeders/RoleSeeder.php)
1. `superadmin`: IT / Developers
2. `admin lppm`: Operational, assigns reviewers
3. `kepala lppm`: LPPM Director, final decisions
4. `dekan`: Faculty Deans, initial approval
5. `dosen`: Lecturers, creators
6. `reviewer`: Expert evaluators
7. `rektor`: University Rector, strategic oversight

## 9. Domain Vocabulary
- **Penelitian**: Research
- **PKM**: Community Service (Pengabdian Masyarakat)
- **TKT**: Technology Readiness Level (0-9)
- **SBK**: Output-based budget standard (Satuan Biaya Keluaran)

## 10. IDE & Agent Configuration
- **Cursor Rules:** Follow `.cursor/rules/laravel-boost.mdc` for boost-specific patterns.
- **Copilot:** Reference `.github/copilot-instructions.md` for quickstart patterns.
- **MCP Tools:**
    - `Serena`: Use for symbol navigation and pattern search.
    - `Laravel Boost`: Use `search-docs` for version-specific Laravel/Livewire help.
    - `Tinker`: Use for executing PHP or debugging Eloquent models.

## 11. Verification Checklist
- [ ] Code formatted with `vendor/bin/pint --dirty`.
- [ ] Explicit return types added to all new methods.
- [ ] Livewire components have a single root element.
- [ ] N+1 queries checked with `with()`.
