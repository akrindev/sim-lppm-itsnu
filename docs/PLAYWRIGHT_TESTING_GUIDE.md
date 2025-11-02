# Playwright Testing Guide for SIM-LPPM ITSNU

## Overview

This comprehensive testing documentation provides detailed test scenarios for the SIM-LPPM ITSNU (Research and Community Service Management System) using Playwright MCP (Multi-Context Playwright). The system is built with Laravel 12, Livewire v3, and manages the complete lifecycle of research proposals and community service projects at ITSNU Pekalongan.

### System Summary

**SIM-LPPM ITSNU** is an academic research and community service management platform that handles:
- Research proposal submission and approval workflows
- Community service project management
- Multi-level approval system (Dekan → Kepala LPPM → Reviewers → Final Approval)
- Team collaboration and budget management
- Progress tracking and reporting

### Technology Stack
- **Backend**: Laravel 12 + Laravel Fortify (Authentication)
- **Frontend**: Livewire v3, Tabler UI + Bootstrap 5
- **Database**: MySQL (development compatible with SQLite for testing)
- **Build Tool**: Bun/NPM

---

## User Roles and Responsibilities

### Role Hierarchy

```
superadmin (System Level)
├── Full system access for IT admin/developer
└── Can modify all data

rektor (University Level - Rector)
├── View all proposals
├── Approve high-priority research
├── Strategic oversight
└── Final approval authority

dekan (Faculty Level - Dean)
├── Faculty-specific proposals
├── Technical approval
├── Faculty budget oversight
└── Faculty research coordination

kepala lppm (LPPM Director - Head of LPPM)
├── LPPM operations oversight
├── Cross-disciplinary coordination
├── Policy implementation
└── Process standardization

admin lppm (General LPPM Admin)
├── User management
├── Basic proposal administration
├── System configuration
└── General maintenance

admin lppm saintek (Science & Technology Admin)
└── Specialization in science/technology proposals

admin lppm dekabita (Social Sciences Admin)
└── Specialization in social sciences/humanities proposals

reviewer (Specialist Reviewer)
├── Technical evaluation
├── Domain expertise input
├── Proposal assessment
└── Review recommendations

dosen (Lecturer/Faculty)
├── Proposal submission
├── Progress reporting
├── Project management
└── Research delivery
```

### Primary Workflows

1. **Proposal Lifecycle**
   - Draft → Submitted → Approved (Dekan) → Need Assignment → Under Review → Reviewed → Completed
   - Alternative paths: Rejected, Revision Needed (loops back to Submitted)

2. **Team Formation**
   - Ketua (Team Leader) creates proposal
   - Anggota (Team Members) are invited
   - All members must accept before submission

3. **Review Process**
   - Admin LPPM assigns reviewers
   - All reviewers must complete reviews
   - Kepala LPPM makes final decision

---

## Test Environment Setup

### Prerequisites

```bash
# Clone repository
git clone https://github.com/akrindev/sim-lppm-itsnu.git
cd sim-lppm-itsnu

# Install dependencies
composer install
bun install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed
```

### Test Users (from seeder)

| Role | Email | Password |
|------|-------|----------|
| Superadmin | superadmin@email.com | password |
| Admin LPPM | admin-lppm@email.com | password |
| Kepala LPPM | kepala-lppm@email.com | password |
| Dosen | dosen1@email.com | password |
| Reviewer | reviewer@email.com | password |
| Rektor | rektor@email.com | password |
| Dekan | dekan@email.com | password |

---

## Role 1: Dosen (Lecturer/Faculty)

### Scenario 1.1: User Authentication - Login

**Purpose**: Verify that dosen can successfully log in to the system.

**Preconditions**: 
- User account exists in database
- User has 'dosen' role assigned

**Steps**:

1. Navigate to login page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/login');
   ```

2. Fill in login credentials
   ```javascript:disable-run
   await page.fill('input[name="email"]', 'dosen1@email.com');
   await page.fill('input[name="password"]', 'password');
   ```

3. Click login button
   ```javascript:disable-run
   await page.click('button[type="submit"]');
   ```

4. Wait for dashboard navigation
   ```javascript:disable-run
   await page.waitForURL('**/dashboard');
   ```

**Expected Outcome**:
- User is redirected to dashboard
- Welcome message displays user's name
- Navigation menu shows dosen-specific options (Penelitian, Pengabdian)
- No error messages appear

**Negative Test Cases**:

a) Invalid email format
```javascript:disable-run
await page.fill('input[name="email"]', 'invalid-email');
await page.fill('input[name="password"]', 'password');
await page.click('button[type="submit"]');
// Expected: Validation error message "The email field must be a valid email address"
```

b) Wrong password
```javascript:disable-run
await page.fill('input[name="email"]', 'dosen1@email.com');
await page.fill('input[name="password"]', 'wrongpassword');
await page.click('button[type="submit"]');
// Expected: Error message "These credentials do not match our records"
```

c) Empty fields
```javascript:disable-run
await page.click('button[type="submit"]');
// Expected: Validation errors for required fields
```

---

### Scenario 1.2: Create Research Proposal - Draft

**Purpose**: Verify dosen can create a new research proposal and save it as draft.

**Preconditions**:
- Dosen is logged in
- Reference data exists (focus areas, themes, research schemes)

**Steps**:

1. Navigate to research proposals page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposals');
   ```

2. Click "Create New Proposal" button
   ```javascript:disable-run
   await page.click('a[href="/research/proposal/create"]');
   ```

3. Fill basic proposal information
   ```javascript:disable-run
   await page.fill('input[name="title"]', 'Implementasi Machine Learning untuk Deteksi Penyakit');
   await page.selectOption('select[name="research_scheme_id"]', { index: 1 });
   await page.selectOption('select[name="focus_area_id"]', { index: 1 });
   await page.selectOption('select[name="theme_id"]', { index: 1 });
   await page.selectOption('select[name="topic_id"]', { index: 1 });
   ```

4. Fill research-specific fields
   ```javascript:disable-run
   await page.fill('textarea[name="background"]', 'Latar belakang penelitian...');
   await page.fill('textarea[name="methodology"]', 'Metodologi penelitian...');
   await page.fill('input[name="sbk_value"]', '50000000');
   await page.fill('input[name="duration_in_years"]', '2');
   ```

5. Save as draft
   ```javascript:disable-run
   await page.click('button:has-text("Simpan Draft")');
   ```

6. Verify success notification
   ```javascript:disable-run
   await page.waitForSelector('.alert-success');
   const successMessage = await page.textContent('.alert-success');
   expect(successMessage).toContain('Draft berhasil disimpan');
   ```

**Expected Outcome**:
- Proposal is saved with status "draft"
- Success notification appears
- Proposal appears in user's proposal list
- User can return to edit the draft later

**Edge Cases**:

a) Maximum title length
```javascript:disable-run
const longTitle = 'A'.repeat(256); // Exceeds typical 255 char limit
await page.fill('input[name="title"]', longTitle);
await page.click('button:has-text("Simpan Draft")');
// Expected: Validation error about maximum length
```

b) Negative budget value
```javascript:disable-run
await page.fill('input[name="sbk_value"]', '-1000000');
// Expected: Validation error or prevented by input type="number" min="0"
```

c) Zero duration
```javascript:disable-run
await page.fill('input[name="duration_in_years"]', '0');
// Expected: Validation error "Duration must be at least 1 year"
```

---

### Scenario 1.3: Add Team Members to Proposal

**Purpose**: Verify dosen can invite team members to join the proposal.

**Preconditions**:
- Dosen has created a draft proposal
- Other dosen users exist in the system

**Steps**:

1. Open proposal detail page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposal/{proposal_id}');
   ```

2. Navigate to team members section
   ```javascript:disable-run
   await page.click('a[href="#team-members"]');
   ```

3. Click "Add Team Member" button
   ```javascript:disable-run
   await page.click('button:has-text("Tambah Anggota")');
   ```

4. Search and select team member
   ```javascript:disable-run
   await page.fill('input[type="search"]', 'dosen2@email.com');
   await page.click('.tom-select-dropdown .option:first-child');
   ```

5. Assign role (ketua/anggota)
   ```javascript:disable-run
   await page.selectOption('select[name="role"]', 'anggota');
   ```

6. Assign tasks
   ```javascript:disable-run
   await page.fill('textarea[name="tasks"]', 'Bertanggung jawab untuk analisis data');
   ```

7. Send invitation
   ```javascript:disable-run
   await page.click('button:has-text("Kirim Undangan")');
   ```

**Expected Outcome**:
- Team member invitation is sent
- Member appears in team list with "pending" status
- Invitation notification is sent to invited member
- Cannot submit proposal until all members accept

**Negative Test Cases**:

a) Add same member twice
```javascript:disable-run
// After adding member once
await page.click('button:has-text("Tambah Anggota")');
await page.fill('input[type="search"]', 'dosen2@email.com');
// Expected: Validation error "Member already added to this proposal"
```

b) Add self as team member
```javascript:disable-run
await page.fill('input[type="search"]', 'dosen1@email.com'); // Same as logged-in user
// Expected: Validation error or option disabled
```

---

### Scenario 1.4: Add Budget Items

**Purpose**: Verify dosen can add budget breakdown items to proposal.

**Preconditions**:
- Proposal exists in draft status
- Budget groups are configured in system

**Steps**:

1. Navigate to budget section
   ```javascript:disable-run
   await page.click('a[href="#budget"]');
   ```

2. Click "Add Budget Item"
   ```javascript:disable-run
   await page.click('button:has-text("Tambah Item Anggaran")');
   ```

3. Fill budget item details
   ```javascript:disable-run
   await page.selectOption('select[name="group"]', 'honor');
   await page.fill('input[name="component"]', 'Honorarium Peneliti');
   await page.fill('textarea[name="item_description"]', 'Honorarium untuk tim peneliti');
   await page.fill('input[name="volume"]', '12'); // months
   await page.fill('input[name="unit_price"]', '1000000');
   ```

4. Verify automatic total calculation
   ```javascript:disable-run
   const totalPrice = await page.inputValue('input[name="total_price"]');
   expect(totalPrice).toBe('12000000'); // volume * unit_price
   ```

5. Save budget item
   ```javascript:disable-run
   await page.click('button:has-text("Simpan")');
   ```

**Expected Outcome**:
- Budget item is added to list
- Total budget is automatically calculated
- Total matches sum of all items
- Budget breakdown is visible in proposal summary

**Edge Cases**:

a) Budget exceeds SBK value
```javascript:disable-run
// If SBK value is 50,000,000
await page.fill('input[name="volume"]', '100');
await page.fill('input[name="unit_price"]', '1000000');
// Expected: Warning that total budget exceeds SBK value
```

b) Decimal volume
```javascript:disable-run
await page.fill('input[name="volume"]', '1.5');
// Expected: Either accepted for partial units or validation error
```

---

### Scenario 1.5: Submit Proposal for Review

**Purpose**: Verify dosen can submit completed proposal for approval.

**Preconditions**:
- Proposal is in draft status
- All required sections are complete
- All team members have accepted invitation

**Steps**:

1. Open proposal detail page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposal/{proposal_id}');
   ```

2. Verify all sections are complete
   ```javascript:disable-run
   const checkmarks = await page.$$('.section-complete-icon');
   expect(checkmarks.length).toBeGreaterThan(0);
   ```

3. Click submit button
   ```javascript:disable-run
   await page.click('button:has-text("Ajukan Proposal")');
   ```

4. Confirm submission in modal
   ```javascript:disable-run
   await page.waitForSelector('.modal.show');
   await page.click('.modal button:has-text("Ya, Ajukan")');
   ```

5. Verify status change
   ```javascript:disable-run
   await page.waitForSelector('.badge:has-text("Diajukan")');
   ```

**Expected Outcome**:
- Proposal status changes to "submitted"
- Notification sent to Dekan role
- User can no longer edit proposal
- Submission timestamp is recorded
- User receives confirmation message

**Blocking Conditions** (Negative Tests):

a) Team member still pending
```javascript:disable-run
// With at least one team member in "pending" status
await page.click('button:has-text("Ajukan Proposal")');
// Expected: Error "Semua anggota tim harus menerima undangan sebelum submit"
```

b) Incomplete required fields
```javascript:disable-run
// With missing methodology section
await page.click('button:has-text("Ajukan Proposal")');
// Expected: Validation errors listing incomplete sections
```

c) Budget not matching SBK value
```javascript:disable-run
// If total budget doesn't match declared SBK value
// Expected: Warning or validation error
```

---

### Scenario 1.6: View Proposal Status and History

**Purpose**: Verify dosen can track proposal progress and view history.

**Preconditions**:
- Proposal has been submitted
- Status transitions have occurred

**Steps**:

1. Navigate to proposals list
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposals');
   ```

2. View proposal with status badge
   ```javascript:disable-run
   const statusBadge = await page.textContent('.proposal-card .badge');
   expect(statusBadge).toContain('Diajukan'); // or other status
   ```

3. Open proposal details
   ```javascript:disable-run
   await page.click('.proposal-card a:has-text("Lihat Detail")');
   ```

4. Check status timeline
   ```javascript:disable-run
   await page.click('a[href="#history"]');
   const timelineItems = await page.$$('.timeline-item');
   expect(timelineItems.length).toBeGreaterThan(0);
   ```

5. Verify each timeline entry shows
   ```javascript:disable-run
   const firstItem = await page.textContent('.timeline-item:first-child');
   // Should contain: status, date, time, user who changed status
   ```

**Expected Outcome**:
- Current status is clearly visible
- Complete history of status changes
- Each change shows who and when
- Color-coded status badges
- Chronological order (newest first)

---

### Scenario 1.7: Respond to Revision Request

**Purpose**: Verify dosen can view feedback and resubmit revised proposal.

**Preconditions**:
- Proposal status is "revision_needed"
- Reviewer comments exist

**Steps**:

1. Open proposal with revision status
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposal/{proposal_id}');
   ```

2. View reviewer comments
   ```javascript:disable-run
   await page.click('a[href="#reviews"]');
   const comments = await page.textContent('.review-comments');
   expect(comments.length).toBeGreaterThan(0);
   ```

3. Click edit proposal
   ```javascript:disable-run
   await page.click('button:has-text("Revisi Proposal")');
   ```

4. Make required changes
   ```javascript:disable-run
   await page.fill('textarea[name="methodology"]', 'Metodologi yang diperbaiki...');
   ```

5. Save changes
   ```javascript:disable-run
   await page.click('button:has-text("Simpan Perubahan")');
   ```

6. Resubmit for review
   ```javascript:disable-run
   await page.click('button:has-text("Submit Ulang")');
   await page.click('.modal button:has-text("Ya, Submit Ulang")');
   ```

**Expected Outcome**:
- Status changes back to "submitted"
- New notification sent to reviewers
- Revision history is preserved
- Previous comments remain visible
- Timestamp of resubmission is recorded

---

### Scenario 1.8: Accept Team Member Invitation

**Purpose**: Verify dosen can accept invitation to join another's proposal.

**Preconditions**:
- User has received team member invitation
- Invitation is in "pending" status

**Steps**:

1. View notifications or invitations page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/notifications');
   ```

2. Click on invitation notification
   ```javascript:disable-run
   await page.click('.notification:has-text("Undangan Tim")');
   ```

3. Review invitation details
   ```javascript:disable-run
   const proposalTitle = await page.textContent('.invitation-proposal-title');
   const assignedTasks = await page.textContent('.invitation-tasks');
   ```

4. Accept invitation
   ```javascript:disable-run
   await page.click('button:has-text("Terima")');
   ```

5. Confirm acceptance
   ```javascript:disable-run
   await page.click('.modal button:has-text("Ya, Terima")');
   ```

**Expected Outcome**:
- Invitation status changes to "accepted"
- Proposal appears in user's proposal list
- Notification sent to proposal owner
- User can now view proposal details
- Proposal can proceed to submission once all accept

**Negative Test Case**:

a) Reject invitation
```javascript:disable-run
await page.click('button:has-text("Tolak")');
await page.fill('textarea[name="rejection_reason"]', 'Tidak tersedia waktu');
await page.click('.modal button:has-text("Ya, Tolak")');
// Expected: Status changes to "rejected", owner is notified, reason is recorded
```

---

## Role 2: Dekan (Dean)

### Scenario 2.1: View Pending Proposals

**Purpose**: Verify Dekan can view all proposals submitted from their faculty awaiting approval.

**Preconditions**:
- Dekan is logged in
- Proposals with status "submitted" exist

**Steps**:

1. Navigate to Dekan dashboard
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dashboard');
   ```

2. View pending proposals count
   ```javascript:disable-run
   const pendingCount = await page.textContent('.stat-card:has-text("Menunggu Persetujuan") .stat-value');
   ```

3. Navigate to proposals page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dekan/proposals');
   ```

4. Filter by submitted status
   ```javascript:disable-run
   await page.selectOption('select[name="status_filter"]', 'submitted');
   ```

5. Verify list displays submitted proposals
   ```javascript:disable-run
   const proposals = await page.$$('.proposal-card');
   expect(proposals.length).toBeGreaterThan(0);
   ```

**Expected Outcome**:
- All submitted proposals are visible
- Each proposal shows: title, submitter, date, status
- Faculty filter shows only own faculty
- Sorting and pagination work correctly

---

### Scenario 2.2: Review and Approve Proposal

**Purpose**: Verify Dekan can review proposal details and approve it.

**Preconditions**:
- Proposal with status "submitted" exists
- Dekan has appropriate permissions

**Steps**:

1. Open proposal detail
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dekan/proposals/{proposal_id}');
   ```

2. Review all proposal sections
   ```javascript:disable-run
   await page.click('a[href="#details"]');
   await page.click('a[href="#team"]');
   await page.click('a[href="#budget"]');
   await page.click('a[href="#schedule"]');
   ```

3. Click approve button
   ```javascript:disable-run
   await page.click('button:has-text("Setujui")');
   ```

4. Confirm approval
   ```javascript:disable-run
   await page.waitForSelector('.modal.show');
   await page.click('.modal button:has-text("Ya, Setujui")');
   ```

5. Verify status change
   ```javascript:disable-run
   await page.waitForSelector('.badge:has-text("Disetujui Dekan")');
   ```

**Expected Outcome**:
- Status changes to "approved"
- Notification sent to Kepala LPPM
- Approval timestamp and approver recorded
- Proposal moves to next workflow stage
- Success message displayed to Dekan

---

### Scenario 2.3: Reject Proposal with Reason

**Purpose**: Verify Dekan can reject proposal with explanation.

**Preconditions**:
- Proposal with status "submitted" exists

**Steps**:

1. Open proposal detail
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dekan/proposals/{proposal_id}');
   ```

2. Click reject button
   ```javascript:disable-run
   await page.click('button:has-text("Tolak")');
   ```

3. Fill rejection reason
   ```javascript:disable-run
   await page.waitForSelector('.modal.show');
   await page.fill('textarea[name="rejection_reason"]', 'Proposal tidak sesuai dengan prioritas fakultas');
   ```

4. Confirm rejection
   ```javascript:disable-run
   await page.click('.modal button:has-text("Ya, Tolak")');
   ```

5. Verify status change
   ```javascript:disable-run
   await page.waitForSelector('.badge:has-text("Ditolak")');
   ```

**Expected Outcome**:
- Status changes to "rejected"
- Rejection reason is saved and visible to submitter
- Notification sent to proposal submitter
- Proposal cannot be edited (terminal state)
- Timestamp of rejection recorded

**Validation Tests**:

a) Empty rejection reason
```javascript:disable-run
await page.click('button:has-text("Tolak")');
await page.click('.modal button:has-text("Ya, Tolak")'); // Without filling reason
// Expected: Validation error "Alasan penolakan wajib diisi"
```

---

### Scenario 2.4: Request Assignment (Skip Kepala LPPM)

**Purpose**: Verify Dekan can fast-track proposal directly to reviewer assignment.

**Preconditions**:
- Proposal with status "submitted" exists
- Workflow allows skip option

**Steps**:

1. Open proposal detail
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dekan/proposals/{proposal_id}');
   ```

2. Click "Need Assignment" button
   ```javascript:disable-run
   await page.click('button:has-text("Butuh Penugasan")');
   ```

3. Confirm decision
   ```javascript:disable-run
   await page.click('.modal button:has-text("Ya")');
   ```

**Expected Outcome**:
- Status changes to "need_assignment"
- Skips "Kepala LPPM Initial Approval" stage
- Notification sent to Admin LPPM for reviewer assignment
- Workflow moves directly to assignment phase

---

## Role 3: Admin LPPM

### Scenario 3.1: Manage Users

**Purpose**: Verify Admin LPPM can create and manage user accounts.

**Preconditions**:
- Admin LPPM is logged in
- Has user management permissions

**Steps**:

1. Navigate to users management
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/users');
   ```

2. Click create user button
   ```javascript:disable-run
   await page.click('button:has-text("Tambah Pengguna")');
   ```

3. Fill user details
   ```javascript:disable-run
   await page.fill('input[name="name"]', 'New Dosen');
   await page.fill('input[name="email"]', 'newdosen@email.com');
   await page.fill('input[name="password"]', 'SecurePass123!');
   await page.fill('input[name="password_confirmation"]', 'SecurePass123!');
   ```

4. Assign role
   ```javascript:disable-run
   await page.selectOption('select[name="role"]', 'dosen');
   ```

5. Fill identity information
   ```javascript:disable-run
   await page.fill('input[name="identity_id"]', '1234567890');
   await page.selectOption('select[name="institution_id"]', { index: 1 });
   await page.selectOption('select[name="study_program_id"]', { index: 1 });
   ```

6. Save user
   ```javascript:disable-run
   await page.click('button:has-text("Simpan")');
   ```

**Expected Outcome**:
- User account is created
- Welcome email sent to new user
- User appears in user list
- Can log in with provided credentials
- Role permissions are active

**Negative Tests**:

a) Duplicate email
```javascript:disable-run
await page.fill('input[name="email"]', 'dosen1@email.com'); // Existing email
await page.click('button:has-text("Simpan")');
// Expected: Validation error "Email already exists"
```

b) Password mismatch
```javascript:disable-run
await page.fill('input[name="password"]', 'Password123');
await page.fill('input[name="password_confirmation"]', 'Password456');
// Expected: Validation error "Password confirmation does not match"
```

---

### Scenario 3.2: Assign Reviewers to Proposal

**Purpose**: Verify Admin LPPM can assign reviewers to proposals needing review.

**Preconditions**:
- Proposal with status "need_assignment" exists
- Reviewer users exist in system

**Steps**:

1. Navigate to proposals needing assignment
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposals');
   await page.selectOption('select[name="status_filter"]', 'need_assignment');
   ```

2. Open proposal detail
   ```javascript:disable-run
   await page.click('.proposal-card:first-child a:has-text("Lihat Detail")');
   ```

3. Navigate to reviewer assignment section
   ```javascript:disable-run
   await page.click('a[href="#reviewers"]');
   ```

4. Click assign reviewers button
   ```javascript:disable-run
   await page.click('button:has-text("Tugaskan Reviewer")');
   ```

5. Select reviewers (minimum 2)
   ```javascript:disable-run
   await page.click('input[type="checkbox"][value="reviewer1-id"]');
   await page.click('input[type="checkbox"][value="reviewer2-id"]');
   ```

6. Confirm assignment
   ```javascript:disable-run
   await page.click('button:has-text("Tugaskan")');
   ```

**Expected Outcome**:
- Status changes to "under_review"
- Notifications sent to assigned reviewers
- Reviewers appear in proposal reviewer list
- Each reviewer can now access the proposal
- Assignment date is recorded

**Validation Tests**:

a) Assign only one reviewer (if minimum is 2)
```javascript:disable-run
await page.click('input[type="checkbox"][value="reviewer1-id"]');
await page.click('button:has-text("Tugaskan")');
// Expected: Validation error "Minimum 2 reviewers required"
```

b) Assign same reviewer twice
```javascript:disable-run
// After already assigning a reviewer
// Expected: Reviewer should not appear in available list
```

---

### Scenario 3.3: Configure System Settings

**Purpose**: Verify Admin LPPM can manage system configuration and reference data.

**Preconditions**:
- Admin LPPM is logged in

**Steps**:

1. Navigate to settings
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/settings');
   ```

2. Manage focus areas
   ```javascript:disable-run
   await page.click('a:has-text("Bidang Fokus")');
   await page.click('button:has-text("Tambah Bidang Fokus")');
   await page.fill('input[name="name"]', 'Teknologi Informasi');
   await page.click('button:has-text("Simpan")');
   ```

3. Add theme under focus area
   ```javascript:disable-run
   await page.click('a:has-text("Tema")');
   await page.click('button:has-text("Tambah Tema")');
   await page.selectOption('select[name="focus_area_id"]', { label: 'Teknologi Informasi' });
   await page.fill('input[name="name"]', 'Artificial Intelligence');
   await page.click('button:has-text("Simpan")');
   ```

4. Add topic under theme
   ```javascript:disable-run
   await page.click('a:has-text("Topik")');
   await page.click('button:has-text("Tambah Topik")');
   await page.selectOption('select[name="theme_id"]', { label: 'Artificial Intelligence' });
   await page.fill('input[name="name"]', 'Machine Learning');
   await page.click('button:has-text("Simpan")');
   ```

**Expected Outcome**:
- Hierarchical taxonomy is maintained
- New items immediately available in proposal forms
- Cascade delete preserves data integrity
- Changes are logged in audit trail

---

## Role 4: Kepala LPPM (Head of LPPM)

### Scenario 4.1: Initial Approval After Dekan

**Purpose**: Verify Kepala LPPM can give initial approval to Dekan-approved proposals.

**Preconditions**:
- Proposal with status "approved" exists (approved by Dekan)
- User has "kepala lppm" or "rektor" role

**Steps**:

1. Navigate to proposals
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposals');
   await page.selectOption('select[name="status_filter"]', 'approved');
   ```

2. Open proposal detail
   ```javascript:disable-run
   await page.click('.proposal-card:first-child a:has-text("Lihat Detail")');
   ```

3. Review approval section
   ```javascript:disable-run
   await page.click('a[href="#approvals"]');
   ```

4. Click approve for assignment
   ```javascript:disable-run
   await page.click('button:has-text("Setujui untuk Penugasan")');
   ```

5. Confirm approval
   ```javascript:disable-run
   await page.click('.modal button:has-text("Ya, Setujui")');
   ```

**Expected Outcome**:
- Status changes to "need_assignment"
- Admin LPPM notified for reviewer assignment
- Approval timestamp recorded
- Proposal proceeds to reviewer assignment phase

---

### Scenario 4.2: Final Decision After Reviews

**Purpose**: Verify Kepala LPPM can make final decision after all reviews are complete.

**Preconditions**:
- Proposal with status "reviewed" exists
- All assigned reviewers have completed reviews

**Steps**:

1. Open reviewed proposal
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposal/{proposal_id}');
   ```

2. Review all reviewer comments
   ```javascript:disable-run
   await page.click('a[href="#reviews"]');
   const reviews = await page.$$('.review-card');
   for (const review of reviews) {
       const rating = await review.$('.review-rating');
       const comments = await review.$('.review-comments');
       // Verify all reviews are present
   }
   ```

3. Make final decision - Approve
   ```javascript:disable-run
   await page.click('button:has-text("Setujui Final")');
   await page.click('.modal button:has-text("Ya, Setujui")');
   ```

**Expected Outcome**:
- Status changes to "completed"
- Notification sent to proposal submitter
- Proposal is finalized and locked
- Project implementation can begin
- Completion timestamp recorded

**Alternative Paths**:

a) Request Revision
```javascript:disable-run
await page.click('button:has-text("Minta Revisi")');
await page.fill('textarea[name="revision_notes"]', 'Perbaikan diperlukan pada metodologi');
await page.click('.modal button:has-text("Ya, Minta Revisi")');
// Expected: Status → "revision_needed", dosen can resubmit
```

b) Final Rejection
```javascript:disable-run
await page.click('button:has-text("Tolak Final")');
await page.fill('textarea[name="rejection_reason"]', 'Tidak memenuhi standar kualitas');
await page.click('.modal button:has-text("Ya, Tolak")');
// Expected: Status → "rejected", terminal state
```

---

### Scenario 4.3: View Reports and Analytics

**Purpose**: Verify Kepala LPPM can access comprehensive reports and analytics.

**Preconditions**:
- Multiple proposals exist with various statuses
- User has reporting permissions

**Steps**:

1. Navigate to reports page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/reports');
   ```

2. View proposal statistics
   ```javascript:disable-run
   const totalProposals = await page.textContent('.stat-card:has-text("Total Proposal") .stat-value');
   const approvedCount = await page.textContent('.stat-card:has-text("Disetujui") .stat-value');
   const rejectedCount = await page.textContent('.stat-card:has-text("Ditolak") .stat-value');
   ```

3. Filter by date range
   ```javascript:disable-run
   await page.fill('input[name="start_date"]', '2024-01-01');
   await page.fill('input[name="end_date"]', '2024-12-31');
   await page.click('button:has-text("Filter")');
   ```

4. Export report
   ```javascript:disable-run
   await page.click('button:has-text("Export Excel")');
   // Wait for download
   const download = await page.waitForEvent('download');
   ```

**Expected Outcome**:
- Accurate statistics displayed
- Charts and visualizations render correctly
- Filters work properly
- Export generates valid file
- Data matches database state

---

## Role 5: Reviewer

### Scenario 5.1: View Assigned Proposals

**Purpose**: Verify reviewer can see proposals assigned to them.

**Preconditions**:
- Reviewer is logged in
- Proposals have been assigned to reviewer

**Steps**:

1. Navigate to review dashboard
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/review');
   ```

2. View pending reviews count
   ```javascript:disable-run
   const pendingCount = await page.textContent('.stat-card:has-text("Menunggu Review") .stat-value');
   ```

3. Click on proposal to review
   ```javascript:disable-run
   await page.click('.proposal-card:first-child a:has-text("Review")');
   ```

**Expected Outcome**:
- Only assigned proposals are visible
- Each proposal shows assignment date
- Status indicates "under_review"
- Clear indication of review deadline (if applicable)

---

### Scenario 5.2: Submit Proposal Review

**Purpose**: Verify reviewer can complete and submit proposal review.

**Preconditions**:
- Proposal is assigned to reviewer
- Proposal status is "under_review"

**Steps**:

1. Open proposal for review
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/review/proposal/{proposal_id}');
   ```

2. Read proposal content
   ```javascript:disable-run
   await page.click('a[href="#abstract"]');
   await page.click('a[href="#methodology"]');
   await page.click('a[href="#budget"]');
   ```

3. Fill review form
   ```javascript:disable-run
   await page.click('a[href="#review-form"]');
   ```

4. Rate different aspects
   ```javascript:disable-run
   await page.selectOption('select[name="originality_score"]', '4');
   await page.selectOption('select[name="methodology_score"]', '5');
   await page.selectOption('select[name="feasibility_score"]', '4');
   await page.selectOption('select[name="impact_score"]', '5');
   ```

5. Provide comments
   ```javascript:disable-run
   await page.fill('textarea[name="strengths"]', 'Metodologi sangat baik dan terstruktur');
   await page.fill('textarea[name="weaknesses"]', 'Perlu penambahan referensi terkini');
   await page.fill('textarea[name="suggestions"]', 'Tambahkan analisis risiko');
   ```

6. Make recommendation
   ```javascript:disable-run
   await page.selectOption('select[name="recommendation"]', 'approve_with_minor_revision');
   ```

7. Submit review
   ```javascript:disable-run
   await page.click('button:has-text("Submit Review")');
   await page.click('.modal button:has-text("Ya, Submit")');
   ```

**Expected Outcome**:
- Review is saved and marked complete
- ProposalReviewer status changes to "completed"
- If all reviewers done, proposal status → "reviewed"
- Notification sent to Admin LPPM/Kepala LPPM
- Reviewer cannot edit after submission
- Review timestamp recorded

**Validation Tests**:

a) Submit without all required fields
```javascript:disable-run
await page.click('button:has-text("Submit Review")');
// Expected: Validation errors for required rating fields
```

b) Submit without recommendation
```javascript:disable-run
// Fill all scores but not recommendation
await page.click('button:has-text("Submit Review")');
// Expected: Validation error "Recommendation is required"
```

---

### Scenario 5.3: Save Review as Draft

**Purpose**: Verify reviewer can save incomplete review to continue later.

**Preconditions**:
- Proposal is assigned to reviewer

**Steps**:

1. Open review form
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/review/proposal/{proposal_id}');
   await page.click('a[href="#review-form"]');
   ```

2. Partially fill review
   ```javascript:disable-run
   await page.selectOption('select[name="originality_score"]', '4');
   await page.fill('textarea[name="strengths"]', 'Partial review...');
   ```

3. Save as draft
   ```javascript:disable-run
   await page.click('button:has-text("Simpan Draft")');
   ```

4. Navigate away and return
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/dashboard');
   await page.goto('http://127.0.0.1:8000/review/proposal/{proposal_id}');
   ```

5. Verify data is preserved
   ```javascript:disable-run
   const savedScore = await page.inputValue('select[name="originality_score"]');
   expect(savedScore).toBe('4');
   ```

**Expected Outcome**:
- Draft is automatically saved
- Data persists across sessions
- Review status remains "in_progress"
- Can resume review anytime
- No notifications sent until final submission

---

## Role 6: Rektor (Rector)

### Scenario 6.1: View All Proposals (High-Level Overview)

**Purpose**: Verify Rektor has oversight access to all proposals across faculties.

**Preconditions**:
- Rektor is logged in
- Proposals from multiple faculties exist

**Steps**:

1. Navigate to proposals overview
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/proposals');
   ```

2. Verify cross-faculty visibility
   ```javascript:disable-run
   const proposals = await page.$$('.proposal-card');
   // Should see proposals from all faculties
   ```

3. Filter by faculty
   ```javascript:disable-run
   await page.selectOption('select[name="faculty_filter"]', 'Faculty A');
   ```

4. View aggregated statistics
   ```javascript:disable-run
   const stats = {
       total: await page.textContent('.stat-total'),
       approved: await page.textContent('.stat-approved'),
       inReview: await page.textContent('.stat-in-review'),
   };
   ```

**Expected Outcome**:
- Can view all proposals regardless of faculty
- Advanced filtering available
- University-wide statistics visible
- Export capabilities for strategic planning
- No editing rights unless explicitly needed

---

### Scenario 6.2: Strategic Approval for Priority Research

**Purpose**: Verify Rektor can approve high-priority or strategic research proposals.

**Preconditions**:
- High-priority proposal exists
- Rektor has approval permissions

**Steps**:

1. Navigate to strategic proposals
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/proposals');
   await page.click('input[type="checkbox"][name="strategic_only"]');
   ```

2. Open proposal detail
   ```javascript:disable-run
   await page.click('.proposal-card:first-child a:has-text("Lihat Detail")');
   ```

3. Review proposal thoroughly
   ```javascript:disable-run
   // Similar to Kepala LPPM review process
   ```

4. Provide strategic approval
   ```javascript:disable-run
   await page.click('button:has-text("Setujui (Rektor)")');
   await page.fill('textarea[name="rector_notes"]', 'Sesuai dengan visi universitas');
   await page.click('.modal button:has-text("Ya, Setujui")');
   ```

**Expected Outcome**:
- Rector-level approval is recorded separately
- Adds weight to proposal
- May unlock additional funding
- Special notation on proposal

---

## Cross-Role Scenarios

### Scenario 7.1: Complete Proposal Workflow (End-to-End)

**Purpose**: Verify entire workflow from draft to completion works seamlessly.

**Actors**: Dosen, Dekan, Admin LPPM, Reviewer, Kepala LPPM

**Steps**:

1. **Dosen creates and submits proposal**
   ```javascript:disable-run
   // Login as dosen1@email.com
   // Create proposal
   // Add team members
   // Submit for approval
   ```

2. **Dekan approves**
   ```javascript:disable-run
   // Login as dekan@email.com
   // Review proposal
   // Click approve
   ```

3. **Kepala LPPM initial approval**
   ```javascript:disable-run
   // Login as kepala-lppm@email.com
   // Approve for assignment
   ```

4. **Admin assigns reviewers**
   ```javascript:disable-run
   // Login as admin-lppm@email.com
   // Assign 2 reviewers
   ```

5. **Reviewers complete reviews**
   ```javascript:disable-run
   // Login as reviewer@email.com
   // Submit review
   // Repeat for second reviewer
   ```

6. **Kepala LPPM final approval**
   ```javascript:disable-run
   // Login as kepala-lppm@email.com
   // Review all reviews
   // Approve proposal
   ```

**Expected Outcome**:
- Status progresses through all stages correctly
- Each role receives appropriate notifications
- No steps can be skipped
- Final status is "completed"
- Complete audit trail exists

**Timeline Verification**:
```javascript:disable-run
// Check proposal history
const timeline = await page.$$('.timeline-item');
const expectedStages = [
    'Draft',
    'Diajukan',
    'Disetujui Dekan',
    'Perlu Persetujuan Anggota',
    'Sedang Direview',
    'Dalam Review',
    'Selesai'
];
expect(timeline.length).toBe(expectedStages.length);
```

---

## Edge Cases and Error Handling

### Scenario 8.1: Concurrent Editing

**Purpose**: Verify system handles concurrent edits gracefully.

**Setup**: Two users editing same proposal simultaneously

**Steps**:

1. User A opens proposal for editing
2. User B opens same proposal
3. User A makes changes and saves
4. User B attempts to save their changes

**Expected Outcome**:
- Optimistic locking prevents data loss
- User B receives "Proposal has been modified" warning
- User B must refresh to see latest changes
- No data corruption occurs

---

### Scenario 8.2: Session Timeout During Form Fill

**Purpose**: Verify data is not lost if session expires.

**Steps**:

1. Start filling long proposal form
   ```javascript:disable-run
   await page.fill('input[name="title"]', 'Long Proposal Title');
   // Fill many fields...
   ```

2. Wait for session timeout (or simulate)
   ```javascript:disable-run
   // Wait beyond session lifetime
   await page.waitForTimeout(1800000); // 30 minutes
   ```

3. Attempt to save
   ```javascript:disable-run
   await page.click('button:has-text("Simpan")');
   ```

**Expected Outcome**:
- User redirected to login
- Form data preserved in browser storage
- After re-login, can restore form data
- Warning message about session timeout

---

### Scenario 8.3: Invalid File Upload

**Purpose**: Verify file upload validation works correctly.

**Steps**:

1. Attempt to upload oversized file
   ```javascript:disable-run
   await page.setInputFiles('input[type="file"]', 'large-file-50mb.pdf');
   // If max is 10MB
   ```

2. Attempt to upload wrong file type
   ```javascript:disable-run
   await page.setInputFiles('input[type="file"]', 'virus.exe');
   ```

**Expected Outcome**:
- Size validation error displayed
- File type validation prevents .exe
- Malware scanning (if implemented)
- Clear error messages
- Form remains intact

---

### Scenario 8.4: Broken Workflow Recovery

**Purpose**: Verify admin can fix proposals stuck in workflow.

**Preconditions**:
- Proposal stuck (e.g., all reviewers assigned but one deleted)

**Steps**:

1. Admin identifies stuck proposal
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/admin/stuck-proposals');
   ```

2. Views issue details
   ```javascript:disable-run
   await page.click('.proposal-card:has-text("ERROR")');
   ```

3. Manually reassigns missing reviewer
   ```javascript:disable-run
   await page.click('button:has-text("Reassign Reviewer")');
   await page.selectOption('select[name="new_reviewer"]', 'replacement-reviewer');
   await page.click('button:has-text("Update")');
   ```

**Expected Outcome**:
- Workflow resumes normally
- Audit log records manual intervention
- Notifications sent to new reviewer
- No data integrity issues

---

## Performance and Load Testing

### Scenario 9.1: Search Performance with Large Dataset

**Purpose**: Verify search remains fast with many proposals.

**Setup**: Database with 1000+ proposals

**Steps**:

1. Perform search with common term
   ```javascript:disable-run
   const startTime = Date.now();
   await page.fill('input[name="search"]', 'machine learning');
   await page.click('button:has-text("Cari")');
   await page.waitForSelector('.search-results');
   const endTime = Date.now();
   const searchTime = endTime - startTime;
   ```

**Expected Outcome**:
- Search completes in < 2 seconds
- Results are paginated
- Relevance sorting works
- No timeout errors

---

### Scenario 9.2: Bulk Operations

**Purpose**: Verify system handles bulk operations efficiently.

**Steps**:

1. Select multiple proposals
   ```javascript:disable-run
   await page.click('input[type="checkbox"][name="select_all"]');
   ```

2. Perform bulk export
   ```javascript:disable-run
   await page.click('button:has-text("Export Selected")');
   const download = await page.waitForEvent('download');
   ```

**Expected Outcome**:
- Operation completes without timeout
- All selected items included in export
- Progress indicator shown for long operations
- System remains responsive

---

## Accessibility Testing

### Scenario 10.1: Keyboard Navigation

**Purpose**: Verify all functionality is accessible via keyboard.

**Steps**:

1. Navigate using Tab key
   ```javascript:disable-run
   await page.keyboard.press('Tab');
   await page.keyboard.press('Tab');
   await page.keyboard.press('Enter'); // Activate button
   ```

2. Use arrow keys in dropdowns
   ```javascript:disable-run
   await page.keyboard.press('ArrowDown');
   await page.keyboard.press('Enter');
   ```

**Expected Outcome**:
- All interactive elements reachable
- Focus indicators clearly visible
- Logical tab order
- Keyboard shortcuts work (if implemented)

---

### Scenario 10.2: Screen Reader Compatibility

**Purpose**: Verify compatibility with screen readers.

**Steps**:

1. Check ARIA labels
   ```javascript:disable-run
   const button = await page.$('button[aria-label="Submit Proposal"]');
   expect(button).toBeTruthy();
   ```

2. Verify semantic HTML
   ```javascript:disable-run
   const mainContent = await page.$('main');
   const navigation = await page.$('nav');
   expect(mainContent && navigation).toBeTruthy();
   ```

**Expected Outcome**:
- Proper ARIA labels on all controls
- Semantic HTML structure
- Alt text on images
- Form labels properly associated

---

## Security Testing

### Scenario 11.1: Authorization Checks

**Purpose**: Verify users cannot access unauthorized resources.

**Steps**:

1. Login as Dosen
   ```javascript:disable-run
   // Login as dosen1@email.com
   ```

2. Attempt to access admin-only page
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/users');
   ```

**Expected Outcome**:
- 403 Forbidden or redirect to dashboard
- Error message about insufficient permissions
- No data leakage in error messages
- Attempt is logged

---

### Scenario 11.2: SQL Injection Prevention

**Purpose**: Verify input is properly sanitized.

**Steps**:

1. Attempt SQL injection in search
   ```javascript:disable-run
   await page.fill('input[name="search"]', "' OR '1'='1");
   await page.click('button:has-text("Cari")');
   ```

**Expected Outcome**:
- Query executes safely
- No database errors exposed
- Input is escaped/parameterized
- No unauthorized data returned

---

### Scenario 11.3: CSRF Protection

**Purpose**: Verify CSRF tokens are validated.

**Steps**:

1. Submit form without CSRF token
   ```javascript:disable-run
   await page.evaluate(() => {
       document.querySelector('input[name="_token"]').remove();
   });
   await page.click('button[type="submit"]');
   ```

**Expected Outcome**:
- Request is rejected
- 419 Page Expired error
- Form must be reloaded
- Session is protected

---

## Mobile Responsiveness

### Scenario 12.1: Mobile Proposal Creation

**Purpose**: Verify proposal creation works on mobile devices.

**Steps**:

1. Set mobile viewport
   ```javascript:disable-run
   await page.setViewportSize({ width: 375, height: 667 }); // iPhone SE
   ```

2. Navigate to create proposal
   ```javascript:disable-run
   await page.goto('http://127.0.0.1:8000/research/proposal/create');
   ```

3. Fill form on mobile
   ```javascript:disable-run
   await page.fill('input[name="title"]', 'Mobile Test Proposal');
   // Continue filling form...
   ```

**Expected Outcome**:
- Form is usable on small screens
- Touch targets are large enough (min 44x44px)
- No horizontal scrolling required
- All features accessible
- Mobile-optimized controls (date pickers, selects)

---

## Notification and Email Testing

### Scenario 13.1: Email Notifications

**Purpose**: Verify email notifications are sent correctly.

**Setup**: Configure mail testing (Mailtrap, MailHog)

**Steps**:

1. Trigger notification event (e.g., proposal submission)
   ```javascript:disable-run
   // Submit proposal as dosen
   await page.click('button:has-text("Ajukan Proposal")');
   ```

2. Check email was sent
   ```javascript:disable-run
   // In testing: check mail logs or Mailtrap inbox
   // Verify email contains:
   // - Correct recipient (Dekan)
   // - Correct subject
   // - Proposal details
   // - Action link
   ```

**Expected Outcome**:
- Email sent to correct recipient
- Contains all necessary information
- Links work correctly
- Professional formatting
- No broken images

---

## Data Integrity and Validation

### Scenario 14.1: Budget Calculations

**Purpose**: Verify budget calculations are accurate.

**Steps**:

1. Add budget items
   ```javascript:disable-run
   await page.fill('input[name="volume"]', '10');
   await page.fill('input[name="unit_price"]', '150000');
   ```

2. Verify automatic calculation
   ```javascript:disable-run
   const totalPrice = await page.inputValue('input[name="total_price"]');
   expect(totalPrice).toBe('1500000');
   ```

3. Check grand total updates
   ```javascript:disable-run
   const grandTotal = await page.textContent('.grand-total');
   expect(grandTotal).toContain('1,500,000');
   ```

**Expected Outcome**:
- Calculations are accurate
- Formatting is correct (thousands separator)
- Updates in real-time
- No rounding errors
- Matches database values

---

## Conclusion

This comprehensive testing guide covers:

- ✅ 6 primary user roles with specific scenarios
- ✅ 50+ detailed test scenarios
- ✅ Positive, negative, and edge case testing
- ✅ Security, accessibility, and performance testing
- ✅ End-to-end workflow validation
- ✅ Playwright code examples for automation

### Best Practices for Implementation

1. **Prioritize Tests**: Start with critical paths (authentication, proposal submission, approvals)
2. **Use Page Object Model**: Create reusable page objects for common components
3. **Environment Management**: Use separate test database to avoid data pollution
4. **Parallel Execution**: Run independent tests in parallel for faster feedback
5. **Visual Regression**: Add screenshot comparison for UI changes
6. **CI/CD Integration**: Automate test execution on every commit/PR
7. **Test Data Management**: Use factories/seeders for consistent test data
8. **Reporting**: Generate detailed reports with screenshots on failures

### Maintenance Notes

- Update tests when workflows change
- Keep test data in sync with production schemas
- Review and refactor flaky tests regularly
- Document known issues and workarounds
- Maintain test coverage above 80%

---

**Document Version**: 1.0  
**Last Updated**: 2024-11-02  
**Maintained By**: QA Team, SIM-LPPM ITSNU
