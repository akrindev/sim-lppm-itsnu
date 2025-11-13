# SIM LPPM ITSNU

Research & Community Service Management System for Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan. A Laravel 12 application that digitizes the entire grant lifecycle from proposal submission through approval workflows to final reporting.

## Core Commands

• Type-check and format: `vendor/bin/pint --dirty`
• Run all tests: `php artisan test`
• Run specific test: `php artisan test --filter=testName`
• Start dev server: `php artisan serve` (currently not used)
• Start Vite: `bun run dev` (currently not used, it build manually by running `bun run build` )
• Start queue worker: `php artisan queue:listen`
• Monitor logs: `php artisan pail`
• Clear all caches: `php artisan optimize:clear`

## Project Layout

```
app/
├─ Livewire/           → Full-stack Livewire v3 components
│  ├─ Actions/         → Reusable action components
│  ├─ Research/        → Research proposal management
│  ├─ CommunityService/→ PKM/Community service management
│  ├─ Reports/         → Dashboard analytics
│  ├─ Users/           → User management
│  └─ Settings/        → Profile, 2FA settings
├─ Models/             → Eloquent models (UUID primary keys)
├─ Notifications/      → Database + email notifications
├─ Policies/           → Authorization policies
└─ Enums/              → PHP enums (ProposalStatus, ProposalType, etc.)

resources/
├─ views/
│  ├─ livewire/        → Livewire component views
│  ├─ components/      → Blade components (Tabler UI)
│  └─ layouts/         → Page layouts
└─ js/                 → Frontend assets (minimal)

database/
├─ migrations/         → Database schema (45+ custom tables)
├─ seeders/            → Data seeders (roles, master data)
└─ factories/          → Model factories for testing

tests/
├─ Feature/            → Feature tests (Pest v4)
└─ Unit/               → Unit tests
```

## Architecture Overview

**Core Workflow:** Proposal lifecycle with multi-stage approval
1. **Dosen** creates proposal (Research or PKM) → DRAFT
2. Invites team members → All must accept → NEED_ASSIGNMENT if rejected
3. Submits → SUBMITTED → **Dekan** approval
4. **Dekan** approves → APPROVED → **Kepala LPPM** initial approval
5. **Kepala LPPM** approves → UNDER_REVIEW → **Admin LPPM** assigns reviewers
6. **Reviewers** evaluate → REVIEWED (when all complete)
7. **Kepala LPPM** final decision → COMPLETED/REVISION_NEEDED/REJECTED

**Key Models:**
- `Proposal` - Polymorphic model (morph: research/community_service)
- `Research` & `CommunityService` - Type-specific data
- `ProposalReviewer` - Review workflow tracking
- `User` - Spatie permission-based roles (7 system roles defined in `RoleSeeder.php`)
- `ActivitySchedule`, `BudgetItem`, `ProposalOutput` - Supporting entities

**7 System Roles** (defined in `database/seeders/RoleSeeder.php`):
1. `superadmin` - IT administrators / developers
2. `admin lppm` - Operational management, reviewer assignment
3. `kepala lppm` - LPPM Director, initial + final approvals
4. `dekan` - Faculty deans, first approval
5. `dosen` - Lecturers, proposal creators
6. `reviewer` - Expert evaluators
7. `rektor` - University Rector, strategic oversight

**Note:** Team member roles (`ketua`/`anggota`) are stored in `proposal_user` pivot table, not as system roles.

## Development Patterns & Constraints

**Laravel 12 Conventions:**
• Constructor property promotion: `public function __construct(public Model $model) { }`
• Explicit return types on all methods
• Use `casts()` method on models (not `$casts` property)
• No `app/Console/Kernel.php` - commands auto-register
• Middleware registered in `bootstrap/app.php`
• Always use curly braces (even for single-line statements)

**Livewire v3 Patterns:**
• Single root element per component
• `wire:key` in loops for performance
• `wire:model.live` for real-time binding
• `$this->dispatch()` for events
• Computed properties for reactive data
• Use `php artisan make:livewire Module/ComponentName` to create components

**Database:**
• UUID primary keys (for some models)
• Eager loading required to prevent N+1 queries
• Use Eloquent relationships with return type hints
• No raw `DB::` facade - use Eloquent or query builder

**Authorization:**
• Spatie permission package (`hasRole()`, `can()`) but it has been extended to support custom logic
• Custom active role system (session-based role switching)
• Policies for complex authorization logic
• Faculty-scoped data access for Dekan roles
• Own-proposal access for Dosen
• Assigned-proposal access for Reviewers

**Code Style:**
• Run `vendor/bin/pint --dirty` before committing
• Follow existing patterns in sibling files
• PHPDoc blocks for complex logic
• Indonesian language for UI text, English for code/tables/fields

## Modern Code Practices & Preferences (2024+)

**CRITICAL: Always prioritize DRY, Centralization, and Reusability**

### **OOP Architecture & Design Patterns**
• **Prefer Abstract Classes for Shared Behavior**
  - Use `App\Livewire\Abstracts\*` for base components
  - Example: `ReportIndex`, `ReportShow` as reusable foundations
  - Child classes override only specific methods

• **Use Traits for Composable Behavior**
  - Split logic by concern: `ReportFilters`, `ReportAuthorization`, `ReportData`
  - Mix and match traits in components as needed
  - Avoid deep inheritance hierarchies (max 2-3 levels)

• **Strategy Pattern for Type Handling**
  - Use configuration arrays to handle different types
  - Centralize type-specific logic in one method
  - Example: `getConfig()` returning view names, routes, validation rules

• **Composition over Inheritance**
  - Build complex behavior from small, focused traits
  - Each trait handles ONE responsibility
  - Prefer dependency injection when needed

### **Livewire v3 Modern Patterns**
• **Form Objects (RECOMMENDED)**
  - Use `Livewire\Form` for complex forms
  - Separate form logic from component logic
  - Cleaner validation: `$this->form->validate()`
  - Example: `ReportForm` class in `app/Livewire/Forms/`

• **Single Responsibility per Component**
  - Index components: List, filter, search only
  - Show components: Display and edit one entity
  - Actions: Single operations (create, update, delete)
  - Generic components for similar functionality

• **Computed Properties for Reactive Data**
  - Use `#[Computed]` attribute for expensive operations
  - Cache results automatically by Livewire
  - Example: `proposals()`, `availableYears()`

• **Events & Dispatching**
  - Use `$this->dispatch()` for component communication
  - Emit events with `#[On]` listeners
  - Example: `dispatch('report-saved')`, `#[On('resetFilters')]`

### **File Organization & Structure**
• **Organize by Functionality, Not Layer**
  ```
  app/Livewire/
  ├─ Abstracts/         → Reusable base classes
  ├─ Traits/            → Composable behaviors
  ├─ Forms/             → Livewire v3 Form objects
  ├─ Research/          → Feature: Research
  ├─ CommunityService/  → Feature: PKM
  └─ Reports/           → Feature: Reporting
  ```

• **Group Related Logic**
  - Keep index/show pairs together
  - Extract common logic to traits
  - Create generic components for similar features

### **Code Quality & Maintainability**
• **Eliminate Duplication (90%+ reduction goal)**
  - 4 similar components → 1 generic + config
  - Repeated methods → Traits
  - Duplicate validation → Form objects
  - Example: 4 Show components (2,414 lines) → 1 Generic (350 lines)

• **Keep Components Small (< 100 lines ideal)**
  - Extract methods to protected/private
  - Move form logic to Form objects
  - Use traits for shared behavior
  - If component > 200 lines → needs refactoring

• **Type Hints & Return Types (REQUIRED)**
  ```php
  protected function getConfig(string $type): array
  protected function loadData(): void
  public function save(): void
  #[Computed]
  public function items(): Collection
  ```

• **Early Returns & Guard Clauses**
  ```php
  // GOOD
  if (!$this->canEdit) {
      abort(403);
  }

  // AVOID
  if ($this->canEdit) {
      // 100 lines of logic
  }
  ```

### **Validation & Error Handling**
• **Validation in Form Objects**
  - Centralize all form validation
  - Reuse across components
  - Clear error messages in Indonesian

• **Database Transactions for Complex Operations**
  ```php
  DB::transaction(function () {
      // Multiple database operations
  });
  ```

• **Use Eloquent Methods over Raw Queries**
  - `firstOrCreate()`, `updateOrCreate()`
  - Relationship methods with scopes
  - Eager loading: `with(['relation'])`

### **Performance Optimizations**
• **Eager Loading (REQUIRED)**
  - Always use `with()` for relationships
  - Preload computed data
  - Use query scopes for reusable filters

• **Computed Properties for Caching**
  - Expensive calculations
  - Database queries
  - Results cached by Livewire

• **Wire:key in Loops (REQUIRED)**
  - Prevent DOM diffing issues
  - Better performance with lists
  - Example: `wire:key="item-{{ $item->id }}"`

### **Authorization & Security**
• **Authorization Traits (PREFERRED)**
  - `ReportAuthorization` for common access logic
  - Consistent permission checks
  - Easy to test in isolation

• **Role-based Access Control**
  - Use Spatie permissions
  - Custom policies for complex logic
  - Faculty-scoped access for Dekan

• **File Upload Security**
  - Validate file types & sizes
  - Use Media Library for storage
  - Clear collections before adding new files

### **Testing & Quality Assurance**
• **Test Form Objects Separately**
  - Validate form logic independently
  - Test validation rules
  - Mock dependencies

• **Test Traits via Test Classes**
  - Create test classes that use traits
  - Test each behavior in isolation

• **Integration Tests for Components**
  - Test end-to-end workflows
  - Verify all features work together

### **Example: Modern Component Structure**
```php
<?php
// Research/ProgressReport/Index.php (36 lines - BEFORE: 100)
class Index extends ReportIndex  // Extends abstract base
{
    protected function getDetailableType(): string
    {
        return 'App\Models\Research';
    }

    protected function getStatusFilter(): array
    {
        return [ProposalStatus::APPROVED, ProposalStatus::COMPLETED];
    }

    // Other methods inherited from ReportIndex
}
```

### **Example: Generic Component (Strategy Pattern)**
```php
<?php
// Reports/Show.php (350 lines - BEFORE: 2,414 across 4 files)
class Show extends ReportShow
{
    use WithFileUploads;
    use ReportAuthorization;

    public function mount(Proposal $proposal, string $type = 'research-progress'): void
    {
        $this->proposal = $proposal;
        $this->config = $this->getConfig($type);  // Strategy pattern
        // ... other logic
    }

    protected function getConfig(string $type): array
    {
        return [
            'research-progress' => [
                'view' => 'livewire.research.progress-report.show',
                'route' => 'research.progress-report.index',
            ],
            // ... other types
        ];
    }
}
```

### **When to Refactor (SMELL Indicators)**
• Same code appears in 2+ files → Extract to trait
• Component > 200 lines → Split responsibilities
• Complex if/else on type → Use Strategy pattern
• Duplicate validation rules → Create Form object
• Deep inheritance chain (4+ levels) → Use composition
• N+1 queries → Use eager loading

### **Migration Strategy for Legacy Code**
1. Identify duplicated code patterns
2. Extract common logic to traits
3. Create abstract base classes
4. Refactor children to extend base
5. Remove duplicate implementations
6. Create generic components for similar features
7. Update routes to use new architecture

## Key Constraints & Business Rules

**Team Management:**
• All team members MUST accept invitation before proposal submission
• If ANY member rejects → status changes to NEED_ASSIGNMENT
• Submitter must fix team (remove rejected, invite new) before resubmitting
• Team roles: `ketua` (leader) or `anggota` (member)

**Approval Workflow:**
• Dekan is FIRST approver (SUBMITTED → APPROVED)
• Dekan can request team fix (→ NEED_ASSIGNMENT) but cannot reject
• Kepala LPPM has TWO approval stages:
  1. Initial: APPROVED → UNDER_REVIEW (triggers reviewer assignment)
  2. Final: REVIEWED → COMPLETED/REVISION_NEEDED/REJECTED
• Only Kepala LPPM can reject proposals
• Revisions return to SUBMITTED status, restart entire workflow

**Review Process:**
• Admin LPPM assigns reviewers AFTER Kepala LPPM initial approval
• Multiple reviewers can be assigned to one proposal
• Proposal status changes to REVIEWED only when ALL reviewers complete
• Reviewers cannot edit reviews after submission (immutable)
• Review deadline reminders sent automatically (3 days before, 1 day after)

**Data Scoping:**
• Dekan: Can only view/approve proposals from own faculty
• Dosen: Can only view own proposals + proposals where they are team members
• Reviewer: Can only view assigned proposals
• Admin LPPM & Kepala LPPM: Institution-wide access

**Budget Management:**
• 2-level hierarchy: Budget Groups → Budget Components
• Volume-based calculation: `total_price = volume × unit_price`
• SBK (Satuan Biaya Keluaran) validation
• Budget items can only be edited in DRAFT status

**Progress Reporting:**
• Only for COMPLETED proposals
• Types: semester_1, semester_2, annual
• Mandatory outputs (required deliverables) + Additional outputs (extra achievements)
• Reports can be saved as DRAFT and submitted later

## External Services

• **Email:** Laravel Mail (for notifications) - queued for async delivery
• **Queue:** Laravel Queue system (database driver default)
• **Database:** MySQL (UUID primary keys throughout)


## Evidence Required for Changes

**Before completing any task:**
• Run code formatter: `vendor/bin/pint --dirty`
• Check for diagnostics errors in modified files
• Verify Livewire component has single root element
• Confirm authorization/scoping works for all roles
• Test status transitions follow workflow rules

**For bug fixes:**
• Failing test that reproduces the bug
• Test now passes after fix

**For new features:**
• New tests demonstrating behavior
• All existing tests still pass
• Code matches existing patterns

**NO test creation/modification required for:**
• `AGENTS.md` updates
• Documentation changes
• Configuration file updates

## Common Gotchas

**Status Transitions:**
• ProposalStatus enum has 9 states: DRAFT, SUBMITTED, NEED_ASSIGNMENT, APPROVED, UNDER_REVIEW, REVIEWED, REVISION_NEEDED, COMPLETED, REJECTED
• Invalid transitions will throw validation errors
• Check `docs/v2/STATUS-TRANSITIONS.md` for valid state machine

**Polymorphic Relationships:**
• Proposal morphs to either `Research` or `CommunityService`
• Always load detailable: `$proposal->load('detailable')`
• Research has: roadmap_data (JSON), tkt_target
• CommunityService has: partner_issue_summary, solution_offered, partner

**Team Acceptance Logic:**
• Query: `proposal_user.status = 'accepted'` for all members
• If ANY `status = 'rejected'` → cannot submit
• If ANY `status = 'pending'` → cannot submit
• Check `$proposal->canBeSubmitted()` helper method

**Faculty Scoping:**
• Dekan must filter: `WHERE submitter.identity.faculty_id = dekan.faculty_id`
• Use query scopes in Proposal model for consistent filtering
• Always eager load: `$query->with(['submitter.identity.faculty'])`

**Notifications:**
• All notifications queued (async processing)
• Database notifications + Email notifications
• Notification types defined in `app/Notifications/`
• Recipients determined by proposal status and actor role

**Livewire Gotchas:**
• Always have single root element in Blade views
• Use `wire:key` in loops to prevent DOM diffing issues
• `wire:model` is deferred by default (use `.live` for real-time)
• Use `$this->dispatch()` not `emit()` (Livewire v3 change)

## Domain Vocabulary

• **Penelitian** = Research (scientific research proposals)
• **PKM** = Pengabdian kepada Masyarakat (community service programs)
• **Dosen** = Lecturer/Researcher
• **Dekan** = Faculty Dean
• **Kepala LPPM** = LPPM Director
• **Admin LPPM** = LPPM Administrator
• **Reviewer** = Expert Evaluator
• **TKT** = Technology Readiness Level (0-9 scale)
• **SBK** = Satuan Biaya Keluaran (output-based budget standards)
• **PRN** = Prioritas Riset Nasional (National Research Priorities)
• **Focus Areas → Themes → Topics** = 3-level taxonomy hierarchy
• **Science Clusters** = 3-level: Bidang → Subbidang → Detail

## Testing & Verification

**Before ANY commit:**
```bash
# Format code
vendor/bin/pint --dirty

# Check no errors
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Manual Testing Checklist:**
1. Can Dosen create and submit proposal?
2. Do all team members receive invitations?
3. Does team rejection trigger NEED_ASSIGNMENT?
4. Can Dekan approve (and only see own faculty)?
5. Does Kepala LPPM initial approval work?
6. Can Admin LPPM assign reviewers?
7. Do reviewers receive notifications?
8. Does proposal become REVIEWED when all reviewers complete?
9. Can Kepala LPPM make final decision?
10. Do revisions restart workflow correctly?

## Documentation References

**Full documentation in:** `docs/v2/`
• **PRD.md** - Product requirements, features, user stories
• **ERD.md** - Database schema (45+ tables)
• **WORKFLOWS.md** - Complete process flows, sequence diagrams
• **ROLES.md** - 7 system roles, permissions matrix
• **STATUS-TRANSITIONS.md** - State machine, valid transitions
• **DATA-STRUCTURES.md** - Research vs PKM differences
• **MASTER-DATA.md** - Taxonomy, reference data
• **NOTIFICATIONS.md** - Notification types, triggers

**Quick References:**
• Laravel 12 docs: Use Laravel Boost MCP `search-docs` tool
• Livewire v3: Use `search-docs` tool with livewire filter
• Spatie Permission: Check existing authorization patterns in codebase


**MCP**: 
• Use `Serena` agent for code search, navigation, and documentation lookup. always use initial prompt for context.
• Use `Laravel Boost` agent for Laravel documentation search, and other Laravel-related queries.
• Use `Sequential Thinking` agent for complex reasoning tasks that require step-by-step analysis.

and other agents as needed.


## Summary

**This is a Laravel 12 application for managing research grants at ITSNU.** It has a complex multi-role approval workflow (Dosen → Dekan → Kepala LPPM → Reviewers → Kepala LPPM final). Uses Livewire 3 for full-stack reactivity, Spatie permissions for RBAC, and UUID primary keys throughout. Always run the formatter. Check workflow rules and status transitions carefully - they are business-critical.
