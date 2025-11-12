# Workflow Documentation v2.0
## SIM LPPM ITSNU - Complete Process Flows

**Document Version:** 2.0  
**Last Updated:** 2025-11-09

---

## Table of Contents
1. [Complete Proposal Lifecycle](#complete-proposal-lifecycle)
2. [Dosen Workflows](#dosen-workflows)
3. [Dekan Workflows](#dekan-workflows)
4. [Kepala LPPM Workflows](#kepala-lppm-workflows)
5. [Admin LPPM Workflows](#admin-lppm-workflows)
6. [Reviewer Workflows](#reviewer-workflows)
7. [Executive Summary](#executive-summary)

---

## Complete Proposal Lifecycle

### Overview Diagram

```mermaid
graph TD
    A[Dosen Creates Proposal] --> B[Status: DRAFT]
    B --> C[Invite Team Members]
    C --> D{All Members<br/>Accepted?}
    D -->|No| E[Status: NEED_ASSIGNMENT]
    E --> C
    D -->|Yes| F[Dosen Submits]
    F --> G[Status: SUBMITTED]
    G --> H[Dekan Reviews]
    H --> I{Dekan<br/>Decision}
    I -->|Approve| J[Status: APPROVED]
    I -->|Need Assignment| E
    I -->|Reject| K[Status: REJECTED]
    J --> L[Kepala LPPM Initial Approval]
    L --> M[Status: UNDER_REVIEW]
    M --> N[Admin LPPM Assigns Reviewers]
    N --> O[Reviewers Evaluate]
    O --> P{All Reviews<br/>Completed?}
    P -->|No| O
    P -->|Yes| Q[Status: REVIEWED]
    Q --> R[Kepala LPPM Final Decision]
    R --> S{Final<br/>Decision}
    S -->|Approve| T[Status: COMPLETED]
    S -->|Revision| U[Status: REVISION_NEEDED]
    S -->|Reject| K
    U --> V[Dosen Revises]
    V --> F
    
    style T fill:#90EE90
    style K fill:#FFB6C1
    style M fill:#87CEEB
    style Q fill:#DDA0DD
```

### Status Progression Table

| Stage | Status                                 | Actor       | Duration  | Next Step                    |
| ----- | -------------------------------------- | ----------- | --------- | ---------------------------- |
| 1     | DRAFT                                  | Dosen       | Variable  | Team invitations             |
| 2     | NEED_ASSIGNMENT                        | Dosen/Team  | 1-2 weeks | Team acceptance              |
| 3     | SUBMITTED                              | Dekan       | 3-5 days  | Dekan review                 |
| 4     | APPROVED                               | Kepala LPPM | 2-3 days  | Kepala LPPM initial approval |
| 5     | UNDER_REVIEW                           | Admin LPPM  | 1-2 days  | Reviewer assignment          |
| 6     | UNDER_REVIEW                           | Reviewers   | 7-14 days | Reviews completion           |
| 7     | REVIEWED                               | Kepala LPPM | 2-3 days  | Final decision               |
| 8     | COMPLETED / REVISION_NEEDED / REJECTED | -           | -         | Terminal or revision loop    |

**Total Average Duration:** 2-3 weeks (without revisions)

---

## Dosen Workflows

### Workflow 1: Create & Submit Proposal

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as System
    participant DB as Database
    participant T as Team Members
    participant N as Notification Service
    participant Dekan
    participant Admin

    %% Creation Phase
    D->>S: Navigate to create proposal page
    S->>DB: Load master data (schemes, focus areas, themes, keywords, etc.)
    DB-->>S: Return reference data
    S-->>D: Display proposal form
    
    D->>S: Fill basic proposal info (title, summary, duration)
    D->>S: Select taxonomy (focus area, theme, topic)
    D->>S: Select scheme, national priority, science clusters
    
    alt Research Proposal
        D->>S: Fill methodology, state-of-the-art, roadmap_data (JSON)
        D->>S: Set TKT target, background
        D->>S: Select macro research group
    else PKM Proposal
        D->>S: Fill partner issue summary, solution offered
        D->>S: Select/add partner organization
    end
    
    D->>S: Add budget items (group, component, volume, unit_price)
    S->>S: Calculate total_price per item
    D->>S: Add activity schedules (year, start_month, end_month)
    D->>S: Add planned outputs (type, category, target_status)
    D->>S: Add research stages (process_name, outputs, indicators)
    D->>S: Add keywords
    
    D->>S: Save as DRAFT
    S->>DB: Save proposal (status: DRAFT)
    DB-->>S: Return proposal_id
    S-->>D: Confirmation: Proposal saved
    
    %% Team Invitation Phase
    D->>S: Invite team members (email, role: ketua/anggota, tasks)
    S->>DB: Create proposal_user records (status: pending)
    S->>N: Trigger TeamInvitationSent notification
    N->>T: Send email + database notification
    
    %% Team Acceptance
    loop For each team member
        T->>S: Login and view invitation
        T->>S: Accept or Reject
        alt Accept
            S->>DB: Update proposal_user.status = 'accepted'
            S->>N: Trigger TeamInvitationAccepted
            N->>D: Notify submitter (acceptance)
        else Reject
            S->>DB: Update proposal_user.status = 'rejected'
            S->>DB: Update proposal.status = 'need_assignment'
            S->>N: Trigger TeamInvitationRejected
            N->>D: Notify submitter (rejection)
        end
    end
    
    %% Submission Phase
    D->>S: Check team status
    S->>S: Validate all team members accepted
    
    alt All Accepted
        D->>S: Click "Submit Proposal"
        S->>S: Validate proposal completeness
        S->>DB: Update proposal.status = 'submitted'
        S->>N: Trigger ProposalSubmitted notification
        N->>Dekan: Email + DB notification
        N->>Admin: Email + DB notification
        N->>T: Email + DB notification (team members)
        S-->>D: Success message + redirect to proposal detail
    else Some Rejected/Pending
        S-->>D: Error: Cannot submit (pending/rejected members)
    end
```

**Key Points:**
- Proposal can be saved as DRAFT multiple times before submission
- Team member acceptance is REQUIRED before submission
- Status transitions to NEED_ASSIGNMENT if any team member rejects
- Submission triggers notifications to Dekan, Admin LPPM, and Team

---

### Workflow 2: Handle Team Rejection & Resubmit

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as System
    participant DB as Database
    participant T1 as Rejected Member
    participant T2 as New Member
    participant N as Notifications

    %% Rejection Scenario
    T1->>S: Reject invitation
    S->>DB: Update proposal_user.status = 'rejected'
    S->>DB: Update proposal.status = 'need_assignment'
    S->>N: Trigger rejection notification
    N->>D: Notify Dosen (team rejection)
    
    %% Fix Team Composition
    D->>S: View proposal (status: NEED_ASSIGNMENT)
    S-->>D: Show rejected member + cannot submit message
    
    D->>S: Remove rejected member
    S->>DB: Delete proposal_user record (rejected member)
    
    D->>S: Invite new team member
    S->>DB: Create new proposal_user (status: pending)
    S->>N: Send invitation
    N->>T2: Email + DB notification
    
    T2->>S: Accept invitation
    S->>DB: Update proposal_user.status = 'accepted'
    S->>N: Trigger acceptance notification
    N->>D: Notify Dosen (acceptance)
    
    %% Resubmission
    D->>S: Check all team members accepted
    S->>S: Validate team status
    D->>S: Resubmit proposal
    S->>DB: Update proposal.status = 'submitted'
    S->>N: Trigger ProposalSubmitted
    N->>Dekan: Notification (submitted)
```

**Key Points:**
- Rejected team members must be replaced before resubmission
- Status automatically changes to NEED_ASSIGNMENT on rejection
- Dosen can remove and invite new members
- Resubmission follows normal submission workflow

---

### Workflow 3: Revision & Resubmission

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as System
    participant DB as Database
    participant KL as Kepala LPPM
    participant N as Notifications

    %% Receive Revision Request
    KL->>S: Mark proposal as REVISION_NEEDED
    S->>DB: Update proposal.status = 'revision_needed'
    S->>N: Trigger FinalDecisionMade notification
    N->>D: Email + DB notification (revision needed)
    
    %% Revision Phase
    D->>S: Login and view notification
    D->>S: Navigate to proposal detail
    S->>DB: Load proposal + reviewer feedback
    S-->>D: Display proposal with reviewer notes
    
    D->>S: Edit proposal sections
    D->>S: Update methodology/solution (based on feedback)
    D->>S: Update budget items (if needed)
    D->>S: Update outputs/schedules (if needed)
    D->>S: Save changes
    S->>DB: Update proposal data
    
    %% Resubmission
    D->>S: Click "Submit Revised Proposal"
    S->>S: Validate completeness
    S->>DB: Update proposal.status = 'submitted'
    S->>N: Trigger ProposalSubmitted
    N->>Dekan: Notification (resubmitted)
    N->>Admin: Notification (resubmitted)
    S-->>D: Success message
    
    Note over S,DB: Proposal re-enters approval workflow<br/>from SUBMITTED status
```

**Key Points:**
- Revised proposals return to SUBMITTED status
- Must go through full approval workflow again (Dekan → Kepala LPPM → Reviewers)
- Dosen can view all reviewer feedback before revising
- No limit on revision attempts (business rule can be added)

---

### Workflow 4: Submit Progress Report

```mermaid
sequenceDiagram
    participant D as Dosen
    participant S as System
    participant DB as Database
    participant A as Admin LPPM
    participant N as Notifications

    %% Report Creation
    D->>S: Navigate to approved proposal
    D->>S: Click "Submit Progress Report"
    S-->>D: Display report form
    
    D->>S: Select reporting period (semester_1/semester_2/annual)
    D->>S: Enter reporting year
    D->>S: Fill summary update
    
    %% Mandatory Outputs
    D->>S: Add mandatory outputs (type, description, status)
    D->>S: Upload evidence files
    S->>DB: Save mandatory_outputs records
    
    %% Additional Outputs
    D->>S: Add additional outputs (optional)
    D->>S: Upload evidence files
    S->>DB: Save additional_outputs records
    
    %% Keywords
    D->>S: Select/add keywords
    S->>DB: Create progress_report_keyword records
    
    %% Submission
    D->>S: Save as DRAFT or Submit
    
    alt Submit
        S->>DB: Create progress_report (status: submitted)
        S->>DB: Set submitted_by, submitted_at
        S->>N: Notify Admin LPPM
        N->>A: Email + DB notification (progress report submitted)
        S-->>D: Success: Report submitted
    else Save Draft
        S->>DB: Create progress_report (status: draft)
        S-->>D: Report saved as draft
    end
```

**Key Points:**
- Progress reports can be semester 1, semester 2, or annual
- Mandatory outputs are required (based on planned outputs)
- Additional outputs showcase extra achievements
- Reports can be saved as draft and submitted later
- Admin LPPM receives notification for review and approval

---

## Dekan Workflows

### Workflow 5: Review & Approve Proposal

```mermaid
sequenceDiagram
    participant N as Notifications
    participant De as Dekan
    participant S as System
    participant DB as Database
    participant KL as Kepala LPPM
    participant D as Dosen (Submitter)
    participant T as Team Members

    %% Receive Notification
    N->>De: Notification: Proposal submitted
    De->>S: Login and view notification
    De->>S: Navigate to proposal detail
    
    %% Review Phase
    S->>DB: Load proposal + detailable (research/PKM)
    S->>DB: Load team members + budget + schedules
    DB-->>S: Return complete proposal data
    S-->>De: Display proposal for review
    
    De->>S: Review title, summary, objectives
    De->>S: Review budget items + total
    De->>S: Review team composition + tasks
    De->>S: Review methodology/solution
    De->>S: Check faculty alignment
    
    %% Decision
    alt Approve
        De->>S: Click "Approve Proposal"
        S->>S: Validate status = 'submitted'
        S->>DB: Update proposal.status = 'approved'
        S->>N: Trigger DekanApprovalDecision (approved)
        N->>KL: Email + DB notification (awaiting initial approval)
        N->>D: Email + DB notification (Dekan approved)
        N->>T: Email + DB notification (team notification)
        S-->>De: Success: Proposal approved
    else Need Team Fix
        De->>S: Click "Need Assignment"
        S->>DB: Update proposal.status = 'need_assignment'
        S->>N: Trigger DekanApprovalDecision (need_assignment)
        N->>D: Email + DB notification (fix team composition)
        N->>T: Email + DB notification (pending members)
        S-->>De: Proposal returned to submitter
    end
```

**Key Points:**
- Dekan is the FIRST approver (after submission)
- Can approve or request team member fixes
- Cannot directly reject (business rule: rejection only by Kepala LPPM)
- Status transitions: SUBMITTED → APPROVED or NEED_ASSIGNMENT
- Notifications sent to Kepala LPPM, Submitter, and Team

---

### Workflow 6: Faculty-Level Filtering

```mermaid
sequenceDiagram
    participant De as Dekan
    participant S as System
    participant DB as Database

    De->>S: Login (role: dekan)
    S->>S: Check active role + faculty assignment
    
    De->>S: Navigate to proposals list
    S->>DB: Query proposals WHERE submitter.faculty = dekan.faculty
    S->>DB: Filter by status (submitted, approved, etc.)
    DB-->>S: Return faculty-scoped proposals
    S-->>De: Display proposals from own faculty only
    
    De->>S: View dashboard statistics
    S->>DB: Aggregate faculty proposals by status
    DB-->>S: Return faculty statistics
    S-->>De: Display faculty-level analytics
```

**Key Points:**
- Dekan can only see proposals from their own faculty
- System automatically filters by faculty affiliation
- Dashboard shows faculty-scoped statistics

---

## Kepala LPPM Workflows

### Workflow 7: Initial Approval (APPROVED → UNDER_REVIEW)

```mermaid
sequenceDiagram
    participant N as Notifications
    participant KL as Kepala LPPM
    participant S as System
    participant DB as Database
    participant A as Admin LPPM

    %% Receive Notification
    N->>KL: Notification: Dekan approved proposal
    KL->>S: Login and view notification
    KL->>S: Navigate to proposal detail
    
    %% Review Phase
    S->>DB: Load proposal + detailable
    S->>DB: Load budget summary + team
    DB-->>S: Return proposal data
    S-->>KL: Display proposal for review
    
    KL->>S: Review proposal alignment with LPPM strategy
    KL->>S: Check budget reasonableness
    KL->>S: Verify Dekan approval
    
    %% Initial Approval Decision
    alt Approve for Review
        KL->>S: Click "Approve for Review"
        S->>S: Validate status = 'approved'
        S->>S: Validate transition to 'under_review'
        S->>DB: Update proposal.status = 'under_review'
        S->>N: Trigger ReviewerAssignment notification
        N->>A: Email + DB notification (assign reviewers)
        S-->>KL: Success: Proposal ready for review assignment
    else Reject
        KL->>S: Click "Reject"
        S->>DB: Update proposal.status = 'rejected'
        S->>N: Trigger rejection notification
        N->>Dosen: Email + DB notification (rejected)
        S-->>KL: Proposal rejected
    end
```

**Key Points:**
- Kepala LPPM provides INITIAL approval AFTER Dekan
- Status transition: APPROVED → UNDER_REVIEW
- This approval triggers Admin LPPM to assign reviewers
- Kepala LPPM can reject at this stage (rare)
- Does NOT assign reviewers directly (Admin LPPM's responsibility)

---

### Workflow 8: Final Decision (REVIEWED → COMPLETED/REVISION_NEEDED)

```mermaid
sequenceDiagram
    participant N as Notifications
    participant KL as Kepala LPPM
    participant S as System
    participant DB as Database
    participant D as Dosen
    participant T as Team
    participant De as Dekan
    participant A as Admin LPPM

    %% Receive Notification
    N->>KL: Notification: All reviewers completed
    KL->>S: Login and view notification
    KL->>S: Navigate to proposal detail
    
    %% Review Summary Phase
    S->>DB: Load proposal + all reviewer recommendations
    DB-->>S: Return proposal + reviews
    S->>S: Generate review summary (approved/revision/rejected counts)
    S-->>KL: Display proposal + review summary
    
    KL->>S: Read each reviewer's notes
    KL->>S: Review recommendations
    KL->>S: Analyze feedback consistency
    
    %% Final Decision
    alt Approve (Complete)
        KL->>S: Click "Approve Proposal"
        KL->>S: Optional: Enter final notes
        S->>S: Validate status = 'reviewed'
        S->>DB: Update proposal.status = 'completed'
        S->>N: Trigger FinalDecisionMade (completed)
        N->>D: Email + DB notification (approved - ready for execution)
        N->>T: Email + DB notification (team notification)
        N->>De: Email + DB notification (Dekan notification)
        N->>A: Email + DB notification (Admin notification)
        S-->>KL: Success: Proposal approved and completed
    else Needs Revision
        KL->>S: Click "Request Revision"
        KL->>S: Enter revision notes
        S->>DB: Update proposal.status = 'revision_needed'
        S->>N: Trigger FinalDecisionMade (revision_needed)
        N->>D: Email + DB notification (revise proposal)
        N->>T: Email + DB notification
        S-->>KL: Proposal returned for revision
    else Reject
        KL->>S: Click "Reject Proposal"
        KL->>S: Enter rejection reason
        S->>DB: Update proposal.status = 'rejected'
        S->>N: Trigger FinalDecisionMade (rejected)
        N->>D: Email + DB notification (rejected)
        N->>T: Email + DB notification
        S-->>KL: Proposal rejected
    end
```

**Key Points:**
- Kepala LPPM makes FINAL decision AFTER all reviews completed
- Can only proceed when proposal.status = 'reviewed'
- Reviews all reviewer recommendations before deciding
- Three options: Approve (COMPLETED), Request Revision, or Reject
- Approved proposals are ready for execution (research/PKM activities)
- Revisions return to Dosen for updates, then restart workflow

---

## Admin LPPM Workflows

### Workflow 9: Assign Reviewers

```mermaid
sequenceDiagram
    participant N as Notifications
    participant A as Admin LPPM
    participant S as System
    participant DB as Database
    participant R as Reviewer(s)

    %% Receive Notification
    N->>A: Notification: Proposal ready for review (Kepala LPPM approved)
    A->>S: Login and view notification
    A->>S: Navigate to proposal detail
    
    %% Load Available Reviewers
    S->>DB: Load proposal details (type, focus area, theme)
    S->>DB: Query users WHERE role = 'reviewer'
    DB-->>S: Return available reviewers
    S-->>A: Display proposal + available reviewers
    
    %% Reviewer Selection
    A->>S: Review proposal type (research vs PKM)
    A->>S: Check focus area / expertise needed
    A->>S: Select reviewer(s) from dropdown
    
    loop For each selected reviewer
        A->>S: Assign reviewer
        S->>S: Validate reviewer not already assigned
        S->>DB: INSERT INTO proposal_reviewer (status: pending)
        S->>N: Trigger ReviewerAssigned notification
        N->>R: Email + DB notification (new assignment)
    end
    
    S-->>A: Success: Reviewer(s) assigned
    
    %% Track Review Progress
    A->>S: Monitor review completion status
    S->>DB: Query proposal_reviewer WHERE status != 'completed'
    DB-->>S: Return pending reviews
    S-->>A: Display review progress dashboard
```

**Key Points:**
- Admin LPPM assigns reviewers AFTER Kepala LPPM initial approval
- Can assign multiple reviewers to one proposal
- Reviewers selected based on expertise (focus area, research type)
- System prevents duplicate assignments (unique constraint)
- Admin can track review progress in dashboard
- Can remove/reassign reviewers if needed (before review completion)

---

### Workflow 10: Manage Master Data

```mermaid
sequenceDiagram
    participant A as Admin LPPM
    participant S as System
    participant DB as Database

    A->>S: Navigate to Settings > Master Data
    S-->>A: Display master data management tabs
    
    alt Manage Focus Areas
        A->>S: Select Focus Areas tab
        S->>DB: Load all focus_areas
        DB-->>S: Return focus areas
        S-->>A: Display focus areas list
        A->>S: Add/Edit/Delete focus area
        S->>DB: CRUD operation on focus_areas
        S-->>A: Success confirmation
    else Manage Themes
        A->>S: Select Themes tab
        S->>DB: Load themes with focus_area relations
        DB-->>S: Return themes
        S-->>A: Display hierarchical themes
        A->>S: Add theme under focus area
        S->>DB: INSERT INTO themes (focus_area_id, name)
        S-->>A: Theme created
    else Manage Keywords
        A->>S: Select Keywords tab
        S->>DB: Load all keywords
        DB-->>S: Return keywords
        S-->>A: Display keywords list
        A->>S: Add keyword
        S->>DB: INSERT INTO keywords (name)
        S-->>A: Keyword created
    else Manage Budget Components
        A->>S: Select Budget Components tab
        S->>DB: Load budget_groups + budget_components
        DB-->>S: Return hierarchy
        S-->>A: Display budget structure
        A->>S: Add component under group
        S->>DB: INSERT INTO budget_components (budget_group_id, code, name, unit)
        S-->>A: Component created
    else Manage Partners
        A->>S: Select Partners tab
        S->>DB: Load all partners
        DB-->>S: Return partners
        S-->>A: Display partners list
        A->>S: Add/Edit partner (name, type, contact info)
        S->>DB: CRUD operation on partners
        S-->>A: Partner saved
    end
```

**Key Points:**
- Admin LPPM manages all reference/master data
- Changes immediately available to all users
- Hierarchical data (focus areas → themes → topics) maintained through FKs
- Budget hierarchy (groups → components) ensures structured budgeting
- Partner management for PKM proposals
- Deletion restricted if data is referenced by proposals

---

## Reviewer Workflows

### Workflow 11: Review Proposal

```mermaid
sequenceDiagram
    participant N as Notifications
    participant R as Reviewer
    participant S as System
    participant DB as Database
    participant KL as Kepala LPPM
    participant A as Admin LPPM

    %% Receive Assignment
    N->>R: Notification: You have been assigned to review a proposal
    R->>S: Login and view notification
    R->>S: Navigate to assigned proposal
    
    %% Load Proposal
    S->>DB: Load proposal + detailable (research/PKM)
    S->>DB: Load team, budget, schedules, outputs
    DB-->>S: Return complete proposal
    S-->>R: Display full proposal for review
    
    %% Review Phase
    R->>S: Update proposal_reviewer.status = 'reviewing'
    S->>DB: Update status
    
    R->>S: Read proposal title, summary, objectives
    R->>S: Review methodology/solution
    R->>S: Evaluate budget reasonableness
    R->>S: Check output feasibility
    R->>S: Review team qualifications
    
    %% Submit Review
    R->>S: Navigate to review form
    S-->>R: Display review form (notes, recommendation)
    
    R->>S: Enter detailed review notes
    R->>S: Select recommendation (approved/revision_needed/rejected)
    R->>S: Click "Submit Review"
    
    S->>S: Validate review completeness
    S->>DB: UPDATE proposal_reviewer SET status='completed', review_notes, recommendation
    
    %% Check All Reviews
    S->>DB: COUNT(*) WHERE proposal_id AND status='completed'
    S->>DB: COUNT(*) WHERE proposal_id (total reviewers)
    
    alt All Reviews Completed
        S->>DB: Update proposal.status = 'reviewed'
        S->>N: Trigger ReviewCompleted notification
        N->>KL: Email + DB notification (ready for final decision)
        N->>A: Email + DB notification (all reviews done)
        S-->>R: Success: Review submitted (all reviews complete)
    else Some Reviews Pending
        S->>N: Trigger ReviewCompleted notification (this reviewer only)
        N->>A: DB notification (one review completed)
        S-->>R: Success: Review submitted (waiting for other reviewers)
    end
```

**Key Points:**
- Reviewers can only view proposals assigned to them
- Status updates from 'pending' → 'reviewing' → 'completed'
- Review notes are detailed feedback for Dosen and Kepala LPPM
- Recommendation is required (approved/revision_needed/rejected)
- Proposal status changes to REVIEWED only when ALL reviewers complete
- Kepala LPPM notified when all reviews done (ready for final decision)

---

### Workflow 12: Review Reminder System

```mermaid
sequenceDiagram
    participant Cron as Scheduler
    participant S as System
    participant DB as Database
    participant N as Notifications
    participant R as Reviewer
    participant A as Admin LPPM

    %% Scheduled Check (Daily)
    Cron->>S: Run review reminder check (daily 8 AM)
    S->>DB: Query proposal_reviewer WHERE status != 'completed'
    DB-->>S: Return pending reviews
    
    loop For each pending review
        S->>S: Calculate days since assignment (created_at)
        
        alt 3 Days Before Deadline
            S->>N: Trigger ReviewReminder notification
            N->>R: Email: "Reminder: Review due in 3 days"
        else 1 Day After Deadline
            S->>N: Trigger ReviewOverdue notification
            N->>R: Email: "Overdue: Review deadline passed"
            N->>A: Email: "Reviewer X has overdue review"
        end
    end
```

**Key Points:**
- Automated reminder system (scheduled job)
- Sends reminder 3 days before deadline
- Sends overdue notice 1 day after deadline
- Admin LPPM notified of overdue reviews for follow-up
- Reviewers can request deadline extension through Admin

---

## Executive Summary

### Complete Approval Chain

**Correct Workflow Sequence:**

```
1. DRAFT → Dosen creates proposal
2. Team invitations → All must ACCEPT before submission
3. SUBMITTED → Dosen submits (if all team accepted)
4. APPROVED → Dekan approves (first approval)
5. UNDER_REVIEW → Kepala LPPM initial approval (second approval)
6. Reviewer Assignment → Admin LPPM assigns reviewers
7. Reviews → Reviewers evaluate and recommend
8. REVIEWED → All reviewers completed (automatic)
9. COMPLETED/REVISION_NEEDED → Kepala LPPM final decision (third approval)
```

### Key Actors & Responsibilities

| Actor            | Primary Responsibility         | Critical Actions                                                                                   |
| ---------------- | ------------------------------ | -------------------------------------------------------------------------------------------------- |
| **Dosen**        | Proposal creation & submission | Create, invite team, submit, revise, progress reports                                              |
| **Team Members** | Collaboration acceptance       | Accept/reject invitations                                                                          |
| **Dekan**        | First-level approval           | Approve proposals from faculty (SUBMITTED → APPROVED)                                              |
| **Kepala LPPM**  | Strategic oversight            | Initial approval (APPROVED → UNDER_REVIEW) + Final decision (REVIEWED → COMPLETED/REVISION_NEEDED) |
| **Admin LPPM**   | Operational coordination       | Assign reviewers, manage master data, user management                                              |
| **Reviewers**    | Expert evaluation              | Review proposals, provide recommendations                                                          |

### Notification Triggers

| Event                        | Recipients                    | Channels   |
| ---------------------------- | ----------------------------- | ---------- |
| Proposal Submitted           | Dekan, Admin LPPM, Team       | Email + DB |
| Team Invitation              | Invited Member                | Email + DB |
| Team Acceptance              | Submitter                     | DB         |
| Team Rejection               | Submitter                     | DB         |
| Dekan Approval               | Kepala LPPM, Submitter, Team  | Email + DB |
| Kepala LPPM Initial Approval | Admin LPPM                    | Email + DB |
| Reviewer Assigned            | Reviewer                      | Email + DB |
| Review Completed (one)       | Admin LPPM                    | DB         |
| All Reviews Completed        | Kepala LPPM, Admin LPPM       | Email + DB |
| Final Decision               | Submitter, Team, Dekan, Admin | Email + DB |
| Review Reminder              | Reviewer                      | Email      |
| Review Overdue               | Reviewer, Admin LPPM          | Email      |

### Alternative Pathways

**Rejection Path:**
```
SUBMITTED → (Dekan rejects) → REJECTED (terminal)
APPROVED → (Kepala LPPM rejects) → REJECTED (terminal)
REVIEWED → (Kepala LPPM rejects) → REJECTED (terminal)
```

**Revision Path:**
```
REVIEWED → (Kepala LPPM requests revision) → REVISION_NEEDED
REVISION_NEEDED → (Dosen revises) → SUBMITTED (restart workflow)
```

**Team Fix Path:**
```
SUBMITTED → (Dekan requests team fix) → NEED_ASSIGNMENT
NEED_ASSIGNMENT → (Dosen fixes team) → SUBMITTED (continue workflow)
Any status → (Team member rejects) → NEED_ASSIGNMENT
```

### Average Processing Times

| Stage                         | Duration      | Bottleneck Risk                  |
| ----------------------------- | ------------- | -------------------------------- |
| Proposal Creation             | 3-7 days      | Dosen workload                   |
| Team Acceptance               | 1-2 weeks     | Team member responsiveness       |
| Dekan Review                  | 3-5 days      | Faculty workload                 |
| Kepala LPPM Initial           | 2-3 days      | Strategic review time            |
| Reviewer Assignment           | 1-2 days      | Admin coordination               |
| Reviewer Evaluation           | 7-14 days     | **HIGH** - Reviewer availability |
| Kepala LPPM Final             | 2-3 days      | Review analysis                  |
| **TOTAL (without revisions)** | **2-3 weeks** | -                                |
| **With one revision cycle**   | **4-6 weeks** | -                                |

### System Automation

**Automated Actions:**
1. Status transition to NEED_ASSIGNMENT on team rejection
2. Status transition to REVIEWED when all reviewers complete
3. Notification sending at each workflow stage
4. Budget total calculation (volume × unit_price)
5. Review deadline reminders (3 days before)
6. Review overdue alerts (1 day after)
7. Daily/weekly summary reports to Admin/Kepala

**Manual Actions:**
1. Proposal creation and content entry
2. Team member acceptance decisions
3. Dekan approval decision
4. Kepala LPPM initial approval
5. Reviewer assignment by Admin
6. Reviewer evaluation and recommendation
7. Kepala LPPM final decision
8. Progress report submission

---

**Document End**
