## Copilot Quickstart for sim-lppm-itsnu

Laravel 12 app for Research & Community Service at ITSNU Pekalongan.

**Stack**: Laravel 12 + Fortify, Livewire v3, Tabler + Bootstrap 5, Pest v4, Pint. No Flux/Volt/Tailwind. Use bun.

**Structure**:
- Modules: `app/Livewire/{Research,CommunityService,Reports,Users,Settings}`; models in `app/Models`
- Routes: `routes/web.php`, `routes/auth.php`; config: `bootstrap/app.php`

**UI**:
- Use Tabler + Bootstrap. Livewire has single root element + `wire:key` in loops
- Use slot attributes (`<x-slot:title>`, etc.) - layout auto-applied
- Use `wire:model.live` for real-time binding, `$this->dispatch()` for events

**Data**: Relationships + `casts()`, eager load (prevent N+1), no `DB::`

**Language**: Indonesian for UI text; English for table/model/field names

**Workflow**:
- Install: `composer install`, `bun install`, copy `.env`, `php artisan key:generate`, `php artisan migrate --seed`
- Dev: `bun run dev`; build: `bun run build`; test: `php artisan test`
- Style: `vendor/bin/pint --dirty` before committing

**AI Agents**: Use Serena MCP for symbol navigation, Laravel Boost MCP for docs/tinker/DB queries

---

## Laravel Guidelines (Concise)

**PHP 8.4**: Use constructor property promotion, explicit return types, PHPDoc blocks. Always use curly braces.

**Laravel 12**: Use `php artisan make:` commands. No middleware in `app/Http/Middleware/`; use `bootstrap/app.php`. Commands auto-register from `app/Console/Commands/`.

**Eloquent**: Use relationships with return type hints, eager loading, `casts()` method. Avoid `DB::`.

**Testing**: Write Pest tests in `tests/Feature`/`tests/Unit`. Use factories. Run: `php artisan test --filter=name`.

**Validation**: Create Form Request classes, not inline validation.

**Configuration**: Use `config('key')`, never `env()` outside config files.

**Tabler Components**: Check `resources/views/components/tabler/` before creating new ones. Use Alpine for client-side, Livewire for server-state interactions.

**Livewire v3**: Use `wire:model.live`, reactive attributes, computed properties. `$this->dispatch()` for events. Single root element + `wire:key` in loops.

**MCP Tools**:
- **Serena MCP**: Symbol navigation, code references, pattern search
- **Laravel Boost MCP**: `search-docs` (version-specific docs), `tinker` (PHP execution), `database-query` (read-only), `list-artisan-commands`, `browser-logs`

**Code Style**: Run `vendor/bin/pint --dirty` before committing.
