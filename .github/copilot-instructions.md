## Copilot Quickstart for sim-lppm-itsnu

Laravel 12 app managing research proposals and community service projects at ITSNU Pekalongan. Handles complete workflow: submission → Dekan approval → Kepala LPPM approval → reviewer assignment → review → final approval.

**Stack**: Laravel 12 + Fortify, Livewire v3, Tabler + Bootstrap 5, Pest v4, Pint. No Flux/Volt/Tailwind. Use bun.

**Structure**:
- Modules: `app/Livewire/{Research,CommunityService,Dekan,Review,Reports,Users,Settings}`
- Models: Polymorphic `Proposal` → `Research` or `CommunityService`; hierarchical `FocusArea` → `Theme` → `Topic`
- Routes: `routes/web.php` (grouped by role/module), `routes/auth.php`; config: `bootstrap/app.php`
- Custom components: `resources/views/components/tabler/` (modal, badge, alert variants)

**Domain Model**:
- **Polymorphic Proposals**: Each proposal is Research XOR CommunityService via `detailable_type`/`detailable_id`
- **Team Membership**: M:N via `proposal_user` pivot (roles: ketua/anggota, status: pending/accepted/rejected)
- **Workflow Blocking**: Cannot submit until all team members accept; cannot final approve until all reviews completed
- **Status Enum**: `ProposalStatus` enum with `->value`, `->label()`, `->color()`, `canTransitionTo()` for workflow validation
- **Hierarchical Taxonomy**: FocusAreas contain Themes; Themes contain Topics (3-level science clusters)

**UI Patterns**:
- Livewire: Single root element, `wire:key` in loops, `x-slot:title/pageTitle/pageSubtitle/pageActions` (not `x-layout`)
- Tabler Components: Check `resources/views/components/tabler/` before creating. Use `x-tabler.modal` with variants (simple/large/success/danger/confirmation), `x-tabler.badge` for status
- Tom Select: Add `x-data="tomSelect"` to `<select>` (not class `tom-select`), works with `wire:model`
- Enum Display: Always use `->label()` for UI (Indonesian), `->value` for comparisons, `->color()` for badges

```.blade.php
<!-- Standard page layout -->
<x-slot:title>Penelitian</x-slot:title>
<x-slot:pageTitle>Daftar Penelitian</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola proposal penelitian Anda dengan fitur lengkap.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <div class="btn-list">
        <a href="{{ route('research.proposal.create') }}" wire:navigate class="btn btn-primary">
            <x-lucide-plus class="icon" />
            Usulan Penelitian Baru
        </a>
    </div>
</x-slot:pageActions>

<!-- Status badge with enum -->
<x-tabler.badge :color="$proposal->status->color()">
    {{ $proposal->status->label() }}
</x-tabler.badge>

<!-- Modal confirmation -->
<x-tabler.modal-confirmation 
    wire:click="approve" 
    title="Setujui Proposal?" 
    message="Yakin menyetujui proposal ini?"
/>
```

**Data Access**:
- Relationships: Use `casts()` method, type-hinted return types, eager loading (prevent N+1)
- Polymorphic: `$proposal->detailable` (instanceof Research/CommunityService)
- Avoid: `DB::` facade (use `Model::query()`)
- Date queries: Use `whereYear()` for production; compatible with SQLite testing via conditional logic

**Role-Based Access**:
- Roles: `dosen`, `dekan`, `kepala lppm`, `rektor`, `admin lppm`, `reviewer`
- Middleware: `role:dekan` in routes, `auth()->user()->hasRole([...])` in views
- Menu: Badge counts for pending items (see `app/View/Composers/MenuComposer.php`)

**Critical Workflows** (see `docs/PROPOSAL_WORKFLOW_IMPLEMENTATION.md`):

**Phase 1: Draft & Team Assembly** (Dosen)
- Create proposal (status: `draft`)
- Add team members via `TeamMemberForm` component → status: `pending`
- **BLOCKING**: All team members must accept (`accepted`) before submission
- Component: `app/Livewire/Research/Proposal/TeamMemberForm.php`

**Phase 2: Submission** (Dosen)
- Submit via `SubmitButton` component → status: `submitted`
- Validates: all team `accepted`, status is `draft` or `revision_needed`
- Notifies: Dekan role

**Phase 3: Dekan Approval** (Dekan)
- View at `/dekan/proposals` (`DekanProposalIndex` component)
- Actions: Approve → `approved` | Need Assignment → `need_assignment` (skip Kepala) | Reject → `rejected`
- All actions use `x-tabler.modal-confirmation`

**Phase 4: Kepala LPPM Initial Approval** (Kepala LPPM/Rektor)
- If status is `approved` from Dekan
- Component: `KepalaLppmInitialApproval` on proposal show page
- Approve → status: `need_assignment`

**Phase 5: Reviewer Assignment** (Admin LPPM)
- When status is `need_assignment`
- Component: `ReviewerAssignment`
- Assign reviewers → status: `under_review`, creates `ProposalReviewer` records

**Phase 6: Review Process** (Reviewers)
- **BLOCKING**: All reviewers must complete reviews
- Component: `ReviewerForm` 
- Submit review → `ProposalReviewer.status`: `completed`, when all done → proposal status: `reviewed`

**Phase 7: Final Decision** (Kepala LPPM/Rektor)
- When status is `reviewed`
- Actions: Approve → `completed` | Revision → `revision_needed` (loops to Phase 2) | Reject → `rejected`

**Status Transitions** (`ProposalStatus` enum):
```
draft → submitted → approved → need_assignment → under_review → reviewed → completed
         ↑            ↓                                              ↓
         └─ revision_needed ←────────────────────────────────────────┘
                                    rejected (terminal state)
```

**Language**: Indonesian for UI text; English for table/model/field names

**Workflow**:
- Install: `composer install`, `bun install`, copy `.env`, `php artisan key:generate`, `php artisan migrate --seed`
- Dev: `bun run dev`; build: `bun run build`; test: `php artisan test --filter=name`
- Style: `vendor/bin/pint --dirty` before committing
- Docs: `docs/` for architecture (ERD, workflows, components); `database/erd-documentation.md` for schema

**AI Agents**: Always use Serena MCP for symbol navigation, Laravel Boost MCP for docs/tinker/DB queries, Linear MCP for task tracking (use Indonesian), sequential thinking for complex tasks.

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
