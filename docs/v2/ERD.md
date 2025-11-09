# Entity-Relationship Diagram (ERD) v2.0
## SIM LPPM ITSNU - Database Schema Documentation

**Document Version:** 2.0  
**Last Updated:** 2025-11-09  
**Database:** MySQL  
**Total Custom Tables:** 45+

---

## Table of Contents
1. [Complete ERD Diagram](#complete-erd-diagram)
2. [Entity Descriptions](#entity-descriptions)
3. [Relationship Summary](#relationship-summary)
4. [Plain-English Summary](#plain-english-summary)

---

## Complete ERD Diagram

```mermaid
erDiagram
    %% ========================================
    %% CORE ENTITIES - Authentication & User Management
    %% ========================================
    
    users ||--|| identities : "has profile"
    users ||--o{ proposals : "submits"
    users ||--o{ proposal_user : "team member of"
    users ||--o{ proposal_reviewer : "reviews"
    users ||--o{ research_stages : "person in charge"
    
    users {
        uuid id PK "Unique user identifier (UUID)"
        string name "User full name"
        string email UK "Unique email address (login credential)"
        string password "Hashed password (bcrypt)"
        timestamp email_verified_at "Email verification timestamp"
        string two_factor_secret "2FA secret key (encrypted, nullable)"
        text two_factor_recovery_codes "2FA recovery codes (encrypted JSON, nullable)"
        timestamp two_factor_confirmed_at "2FA activation timestamp (nullable)"
        string remember_token "Session persistence token (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    identities }o--|| users : "belongs to"
    identities }o--|| institutions : "affiliated with"
    identities }o--|| study_programs : "enrolled in"
    identities }o--|| faculties : "belongs to"
    
    identities {
        uuid id PK "Unique identity identifier (UUID)"
        uuid user_id FK "FK to users.id (one-to-one)"
        string identity_id "NIDN (10 digits) or NIM (16 digits)"
        string sinta_id "Sinta ID for researchers (optional)"
        string type "User type: dosen or mahasiswa"
        text address "Full residential address (nullable)"
        string birthplace "Place of birth (nullable)"
        date birthdate "Date of birth (nullable)"
        bigint institution_id FK "FK to institutions.id (nullable, set null)"
        bigint study_program_id FK "FK to study_programs.id (nullable, set null)"
        bigint faculty_id FK "FK to faculties.id (nullable, set null)"
        string profile_picture "Profile image file path (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% PROPOSAL SYSTEM - Central Entity
    %% ========================================
    
    proposals ||--o| research : "morphs to (detailable)"
    proposals ||--o| community_services : "morphs to (detailable)"
    proposals }o--|| research_schemes : "follows scheme"
    proposals }o--|| focus_areas : "categorized by"
    proposals }o--|| themes : "themed as"
    proposals }o--|| topics : "specific topic"
    proposals }o--|| national_priorities : "aligns with PRN"
    proposals }o--|| science_clusters : "cluster level 1"
    proposals }o--|| science_clusters : "cluster level 2"
    proposals }o--|| science_clusters : "cluster level 3"
    proposals ||--o{ proposal_outputs : "produces"
    proposals ||--o{ budget_items : "budgets"
    proposals ||--o{ activity_schedules : "schedules"
    proposals ||--o{ research_stages : "stages"
    proposals ||--o{ progress_reports : "reports"
    proposals ||--o{ proposal_reviewer : "reviewed by"
    
    proposals {
        uuid id PK "Unique proposal identifier (UUID)"
        string title "Proposal title/name"
        uuid submitter_id FK "FK to users.id (proposal owner, cascade delete)"
        string detailable_type "Polymorphic type: App-Models-Research or App-Models-CommunityService"
        uuid detailable_id "Polymorphic FK to research.id or community_services.id"
        bigint research_scheme_id FK "FK to research_schemes.id (nullable, set null)"
        bigint focus_area_id FK "FK to focus_areas.id (nullable, set null)"
        bigint theme_id FK "FK to themes.id (nullable, set null)"
        bigint topic_id FK "FK to topics.id (nullable, set null)"
        bigint national_priority_id FK "FK to national_priorities.id (nullable, set null)"
        bigint cluster_level1_id FK "FK to science_clusters.id Level-1 (nullable, set null)"
        bigint cluster_level2_id FK "FK to science_clusters.id Level-2 (nullable, set null)"
        bigint cluster_level3_id FK "FK to science_clusters.id Level-3 (nullable, set null)"
        decimal sbk_value "SBK value (Satuan Biaya Keluaran) in IDR (nullable)"
        integer duration_in_years "Project duration in years (default: 1)"
        text summary "Proposal summary/abstract (nullable)"
        enum status "draft | submitted | need_assignment | approved | under_review | reviewed | revision_needed | completed | rejected"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% RESEARCH-SPECIFIC DATA
    %% ========================================
    
    research }o--|| macro_research_groups : "categorized by"
    
    research {
        uuid id PK "Unique research identifier (UUID)"
        bigint macro_research_group_id FK "FK to macro_research_groups.id (nullable, set null)"
        integer final_tkt_target "Target TKT level 0-9 (nullable)"
        longText background "Research background and rationale"
        longText state_of_the_art "Literature review and current state"
        longText methodology "Research methodology and approach"
        json roadmap_data "Multi-year roadmap JSON structure"
        string substance_file "Uploaded substance document path (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% PKM-SPECIFIC DATA
    %% ========================================
    
    community_services }o--o| partners : "main partner"
    
    community_services {
        uuid id PK "Unique community service identifier (UUID)"
        bigint partner_id FK "FK to partners.id (main partner, nullable, set null)"
        text partner_issue_summary "Community problem description (nullable)"
        text solution_offered "Proposed solution approach (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% TEAM & COLLABORATION
    %% ========================================
    
    proposal_user }o--|| proposals : "belongs to"
    proposal_user }o--|| users : "is user"
    
    proposal_user {
        bigint id PK "Unique team membership identifier"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        uuid user_id FK "FK to users.id (cascade delete)"
        enum role "Team role: ketua (leader) or anggota (member)"
        text tasks "Assigned responsibilities and tasks (nullable)"
        enum status "Invitation status: pending | accepted | rejected (default: pending)"
        timestamp created_at "Team invitation timestamp"
        timestamp updated_at "Last status update timestamp"
    }
    
    proposal_reviewer }o--|| proposals : "belongs to"
    proposal_reviewer }o--|| users : "is reviewer"
    
    proposal_reviewer {
        bigint id PK "Unique reviewer assignment identifier"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        uuid user_id FK "FK to users.id (cascade delete, reviewer)"
        enum status "Review status: pending | reviewing | completed (default: pending)"
        text review_notes "Detailed review feedback (nullable)"
        enum recommendation "approved | rejected | revision_needed (nullable)"
        timestamp created_at "Assignment timestamp"
        timestamp updated_at "Last update timestamp (review completion)"
    }
    
    %% ========================================
    %% BUDGET MANAGEMENT
    %% ========================================
    
    budget_groups ||--o{ budget_components : "contains"
    budget_items }o--|| budget_groups : "uses group"
    budget_items }o--|| budget_components : "uses component"
    budget_items }o--|| proposals : "belongs to"
    
    budget_groups {
        bigint id PK "Unique budget group identifier"
        string code UK "Unique group code (e.g., HONOR, BAHAN)"
        string name "Budget group name"
        text description "Budget group description (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    budget_components {
        bigint id PK "Unique budget component identifier"
        bigint budget_group_id FK "FK to budget_groups.id (cascade delete)"
        string code "Component code (unique within group)"
        string name "Budget component name"
        text description "Component description (nullable)"
        string unit "Measurement unit (e.g., per sesi, per liter)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    budget_items {
        uuid id PK "Unique budget item identifier (UUID)"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        bigint budget_group_id FK "FK to budget_groups.id"
        bigint budget_component_id FK "FK to budget_components.id"
        text item_description "Detailed item description"
        integer volume "Quantity/volume"
        decimal unit_price "Price per unit in IDR"
        decimal total_price "Total price (volume x unit_price, auto-calculated)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% TAXONOMY SYSTEM
    %% ========================================
    
    focus_areas ||--o{ themes : "contains"
    focus_areas ||--o{ proposals : "categorizes"
    themes }o--|| focus_areas : "belongs to"
    themes ||--o{ topics : "contains"
    themes ||--o{ proposals : "categorizes"
    topics }o--|| themes : "belongs to"
    topics ||--o{ proposals : "categorizes"
    
    focus_areas {
        bigint id PK "Unique focus area identifier"
        string name "Focus area name (e.g., Kesehatan, Teknologi)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    themes {
        bigint id PK "Unique theme identifier"
        bigint focus_area_id FK "FK to focus_areas.id (cascade delete)"
        string name "Theme name (child of focus area)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    topics {
        bigint id PK "Unique topic identifier"
        bigint theme_id FK "FK to themes.id (cascade delete)"
        string name "Topic name (child of theme)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% SCIENCE CLUSTERS
    %% ========================================
    
    science_clusters ||--o{ science_clusters : "parent-child (self-referencing)"
    
    science_clusters {
        bigint id PK "Unique science cluster identifier"
        string name "Cluster name"
        integer level "Hierarchy level: 1, 2, or 3"
        bigint parent_id FK "FK to science_clusters.id (parent, nullable for level 1)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% REFERENCE DATA
    %% ========================================
    
    research_schemes ||--o{ proposals : "categorizes"
    national_priorities ||--o{ proposals : "aligns with"
    macro_research_groups ||--o{ research : "categorizes"
    
    research_schemes {
        bigint id PK "Unique research scheme identifier"
        string name "Scheme name (e.g., Penelitian Dasar, Terapan)"
        string strata "Strata level (nullable)"
        text description "Scheme description (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    national_priorities {
        bigint id PK "Unique national priority identifier"
        string name "Priority name (PRN alignment)"
        text description "Priority description (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    macro_research_groups {
        bigint id PK "Unique macro research group identifier"
        string name "Research group name"
        text description "Group description (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% PROPOSAL SUPPORTING DATA
    %% ========================================
    
    proposal_outputs }o--|| proposals : "belongs to"
    activity_schedules }o--|| proposals : "belongs to"
    research_stages }o--|| proposals : "belongs to"
    research_stages }o--|| users : "person in charge"
    
    proposal_outputs {
        uuid id PK "Unique output identifier (UUID)"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        integer output_year "Target year for output"
        enum category "required | additional"
        string type "Output type (journal, patent, book, etc.)"
        string target_status "Target achievement (Q1, Q2, Granted, etc.)"
        text output_description "Detailed output description (nullable)"
        string journal_name "Journal/venue name (nullable)"
        date estimated_date "Estimated completion date (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    activity_schedules {
        uuid id PK "Unique activity identifier (UUID)"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        string activity_name "Activity description"
        integer year "Activity year"
        integer start_month "Start month (1-12)"
        integer end_month "End month (1-12)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    research_stages {
        uuid id PK "Unique research stage identifier (UUID)"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        integer stage_number "Sequential stage number"
        string process_name "Stage/process name"
        text outputs "Expected stage outputs (nullable)"
        text indicator "Success indicators (nullable)"
        uuid person_in_charge_id FK "FK to users.id (team member, nullable, set null)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% PROGRESS TRACKING
    %% ========================================
    
    progress_reports }o--|| proposals : "belongs to"
    progress_reports }o--|| users : "submitted by"
    progress_reports ||--o{ mandatory_outputs : "has mandatory"
    progress_reports ||--o{ additional_outputs : "has additional"
    
    progress_reports {
        uuid id PK "Unique progress report identifier (UUID)"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        text summary_update "Updated project summary (nullable)"
        integer reporting_year "Year of report"
        enum reporting_period "semester_1 | semester_2 | annual"
        enum status "draft | submitted | approved (default: draft)"
        uuid submitted_by FK "FK to users.id (submitter, nullable)"
        timestamp submitted_at "Submission timestamp (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    mandatory_outputs {
        uuid id PK "Unique mandatory output identifier (UUID)"
        uuid progress_report_id FK "FK to progress_reports.id (cascade delete)"
        string output_type "Type of output"
        text description "Output description"
        string status "Completion status"
        string evidence_file "Supporting document path (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    additional_outputs {
        uuid id PK "Unique additional output identifier (UUID)"
        uuid progress_report_id FK "FK to progress_reports.id (cascade delete)"
        string output_type "Type of output"
        text description "Output description"
        string status "Completion status"
        string evidence_file "Supporting document path (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% PARTNERS & KEYWORDS
    %% ========================================
    
    partners ||--o{ community_services : "main partner"
    proposal_partner }o--|| proposals : "belongs to"
    proposal_partner }o--|| partners : "is partner"
    proposal_keyword }o--|| proposals : "belongs to"
    proposal_keyword }o--|| keywords : "is keyword"
    progress_report_keyword }o--|| progress_reports : "belongs to"
    progress_report_keyword }o--|| keywords : "is keyword"
    
    partners {
        bigint id PK "Unique partner identifier"
        string name "Organization name"
        string type "Organization type (NGO, Community, Government, etc.)"
        text address "Organization address"
        string contact_person "Contact person name (nullable)"
        string phone "Contact phone number (nullable)"
        string email "Contact email address (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    proposal_partner {
        uuid proposal_id PK,FK "FK to proposals.id (cascade delete)"
        bigint partner_id PK,FK "FK to partners.id (cascade delete)"
        timestamp created_at "Relationship creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    keywords {
        bigint id PK "Unique keyword identifier"
        string name "Keyword text"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    proposal_keyword {
        bigint id PK "Unique proposal-keyword relationship identifier"
        uuid proposal_id FK "FK to proposals.id (cascade delete)"
        bigint keyword_id FK "FK to keywords.id (cascade delete)"
        timestamp created_at "Relationship creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    progress_report_keyword {
        bigint id PK "Unique report-keyword relationship identifier"
        uuid progress_report_id FK "FK to progress_reports.id (cascade delete)"
        bigint keyword_id FK "FK to keywords.id (cascade delete)"
        timestamp created_at "Relationship creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% ORGANIZATIONAL STRUCTURE
    %% ========================================
    
    institutions ||--o{ study_programs : "offers"
    institutions ||--o{ identities : "affiliated with"
    faculties ||--o{ study_programs : "manages"
    faculties ||--o{ identities : "belongs to"
    study_programs }o--|| institutions : "belongs to institution"
    study_programs }o--|| faculties : "belongs to faculty"
    study_programs ||--o{ identities : "enrolled in"
    
    institutions {
        bigint id PK "Unique institution identifier"
        string name "Institution name"
        string type "Institution type"
        text address "Institution address"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    faculties {
        bigint id PK "Unique faculty identifier"
        string code "Faculty code (e.g., SAINTEK, DEKABITA)"
        string name "Faculty name"
        text description "Faculty description (nullable)"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    study_programs {
        bigint id PK "Unique study program identifier"
        bigint institution_id FK "FK to institutions.id (cascade delete)"
        bigint faculty_id FK "FK to faculties.id"
        string name "Study program name"
        timestamp created_at "Record creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
    
    %% ========================================
    %% NOTIFICATIONS
    %% ========================================
    
    notifications {
        uuid id PK "Unique notification identifier (UUID)"
        string type "Notification class name"
        string notifiable_type "Polymorphic: notifiable model type"
        uuid notifiable_id "Polymorphic: notifiable model ID"
        json data "Notification payload (title, message, link, etc.)"
        timestamp read_at "Read timestamp (nullable)"
        timestamp created_at "Notification creation timestamp"
        timestamp updated_at "Last modification timestamp"
    }
```

---

## Entity Descriptions

### Core Tables

#### `users`
**Primary authentication and user management table**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| name | string | User full name |
| email | string | Unique email (login credential) |
| password | string | Hashed password |
| email_verified_at | timestamp | Email verification timestamp |
| two_factor_secret | string | 2FA secret (encrypted) |
| two_factor_recovery_codes | text | 2FA recovery codes (encrypted) |
| two_factor_confirmed_at | timestamp | 2FA activation timestamp |
| remember_token | string | Session token |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:1 with `identities` (extended profile)
- 1:N with `proposals` (submitter)
- M:N with `proposals` via `proposal_user` (team member)
- 1:N with `proposal_reviewer` (reviewer assignments)
- 1:N with `research_stages` (person in charge)
- M:N with `roles` and `permissions` (Spatie Permission, excluded from ERD)

---

#### `identities`
**Extended user profile with academic/institutional information**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| user_id | uuid | FK to users |
| identity_id | string | NIDN (10 digits) or NIM (16 digits) |
| sinta_id | string | Sinta ID (optional, for researchers) |
| type | string | "dosen" or "mahasiswa" |
| address | text | Full address |
| birthplace | string | Place of birth |
| birthdate | date | Date of birth |
| institution_id | bigint | FK to institutions |
| study_program_id | bigint | FK to study_programs |
| faculty_id | bigint | FK to faculties |
| profile_picture | string | File path to profile image |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `users`
- N:1 with `institutions`
- N:1 with `study_programs`
- N:1 with `faculties`

---

#### `proposals`
**Central entity for both Research and PKM proposals (polymorphic parent)**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| title | string | Proposal title |
| submitter_id | uuid | FK to users (proposal owner) |
| detailable_type | string | Polymorphic type: "App\Models\Research" or "App\Models\CommunityService" |
| detailable_id | uuid | Polymorphic ID (FK to research/community_services) |
| research_scheme_id | bigint | FK to research_schemes |
| focus_area_id | bigint | FK to focus_areas |
| theme_id | bigint | FK to themes |
| topic_id | bigint | FK to topics |
| national_priority_id | bigint | FK to national_priorities |
| cluster_level1_id | bigint | FK to science_clusters (level 1) |
| cluster_level2_id | bigint | FK to science_clusters (level 2) |
| cluster_level3_id | bigint | FK to science_clusters (level 3) |
| sbk_value | decimal(15,2) | SBK (Satuan Biaya Keluaran) value |
| duration_in_years | integer | Project duration (default: 1) |
| summary | text | Proposal summary/abstract |
| status | enum | ProposalStatus: draft, submitted, need_assignment, approved, under_review, reviewed, revision_needed, completed, rejected |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `users` (submitter)
- 1:1 polymorphic with `research` OR `community_services`
- 1:N with `proposal_outputs`, `budget_items`, `activity_schedules`, `research_stages`, `progress_reports`, `proposal_reviewer`
- M:N with `users` via `proposal_user` (team members)
- M:N with `keywords` via `proposal_keyword`
- M:N with `partners` via `proposal_partner`
- N:1 with taxonomy tables (research_schemes, focus_areas, themes, topics, national_priorities, science_clusters)

---

#### `research`
**Research-specific proposal data (polymorphic child)**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| macro_research_group_id | bigint | FK to macro_research_groups |
| final_tkt_target | integer | Target TKT level (0-9) |
| background | longText | Research background |
| state_of_the_art | longText | Literature review |
| methodology | longText | Research methods |
| roadmap_data | json | Multi-year roadmap structure |
| substance_file | string | Uploaded document path |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Roadmap JSON Structure:**
```json
{
  "year_1": {
    "activities": ["Activity 1", "Activity 2"],
    "targets": ["Target 1", "Target 2"],
    "tkt_level": 3
  },
  "year_2": {
    "activities": ["Activity 3", "Activity 4"],
    "targets": ["Target 3", "Target 4"],
    "tkt_level": 5
  }
}
```

**Relationships:**
- 1:1 morphOne with `proposals` (detailable)
- N:1 with `macro_research_groups`

---

#### `community_services`
**PKM-specific proposal data (polymorphic child)**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| partner_id | bigint | FK to partners (main partner, optional) |
| partner_issue_summary | text | Community problem description |
| solution_offered | text | Proposed solution approach |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:1 morphOne with `proposals` (detailable)
- N:1 with `partners` (main partner)

---

### Taxonomy Tables

#### `focus_areas` (Bidang Fokus)
**Top-level research categorization**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Focus area name (e.g., "Kesehatan", "Teknologi") |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `themes`
- 1:N with `proposals`

---

#### `themes` (Tema)
**Second-level categorization (child of focus_areas)**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| focus_area_id | bigint | FK to focus_areas (cascade delete) |
| name | string | Theme name (e.g., "Gizi", "Epidemiologi") |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `focus_areas`
- 1:N with `topics`
- 1:N with `proposals`

---

#### `topics` (Topik)
**Third-level categorization (child of themes)**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| theme_id | bigint | FK to themes (cascade delete) |
| name | string | Topic name (e.g., "Malnutrisi Anak", "Obesitas") |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `themes`
- 1:N with `proposals`

---

#### `science_clusters` (Rumpun Ilmu)
**3-level hierarchical science classification**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Cluster name |
| level | integer | Hierarchy level (1, 2, or 3) |
| parent_id | bigint | FK to science_clusters (self-referencing, nullable) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Hierarchy:**
- **Level 1:** Bidang (e.g., "Ilmu Alam")
- **Level 2:** Subbidang (e.g., "Fisika", "Kimia")
- **Level 3:** Detail (e.g., "Fisika Kuantum", "Kimia Organik")

**Relationships:**
- Self-referencing 1:N (parent-child)
- 1:N with `proposals` (via 3 separate FKs: cluster_level1_id, cluster_level2_id, cluster_level3_id)

---

#### `research_schemes` (Skema Penelitian)
**Research classification schemes**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Scheme name (e.g., "Penelitian Dasar", "Penelitian Terapan") |
| strata | string | Strata level (optional) |
| description | text | Scheme description |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `proposals`

---

#### `national_priorities` (Prioritas Riset Nasional)
**National research priority alignment**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Priority name |
| description | text | Priority description |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `proposals`

---

### Budget Tables

#### `budget_groups` (Kelompok Anggaran)
**Top-level budget categorization**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| code | string(10) | Unique group code (e.g., "HONOR", "BAHAN") |
| name | string | Group name (e.g., "Honorarium", "Bahan Habis Pakai") |
| description | text | Group description |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `budget_components`
- 1:N with `budget_items`

---

#### `budget_components` (Komponen Anggaran)
**Second-level budget categorization (child of budget_groups)**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| budget_group_id | bigint | FK to budget_groups (cascade delete) |
| code | string(10) | Component code (unique within group) |
| name | string | Component name (e.g., "Narasumber", "Bahan Kimia") |
| description | text | Component description |
| unit | string | Measurement unit (e.g., "per sesi", "per liter") |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Unique Constraint:** (budget_group_id, code)

**Relationships:**
- N:1 with `budget_groups`
- 1:N with `budget_items`

---

#### `budget_items` (Item Anggaran)
**Specific budget line items in proposals**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| budget_group_id | bigint | FK to budget_groups |
| budget_component_id | bigint | FK to budget_components |
| item_description | text | Detailed item description |
| volume | integer | Quantity |
| unit_price | decimal(15,2) | Price per unit |
| total_price | decimal(15,2) | Calculated total (volume × unit_price) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `proposals`
- N:1 with `budget_groups`
- N:1 with `budget_components`

---

### Collaboration Tables

#### `partners` (Mitra)
**External partner organizations (for PKM)**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Organization name |
| type | string | Organization type (e.g., "NGO", "Community", "Government") |
| address | text | Full address |
| contact_person | string | Contact person name |
| phone | string | Phone number |
| email | string | Email address |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `community_services` (main partner)
- M:N with `proposals` via `proposal_partner`

---

#### `proposal_partner` (Pivot Table)
**Many-to-many relationship between proposals and partners**

| Column | Type | Description |
|--------|------|-------------|
| proposal_id | uuid | FK to proposals (cascade delete) |
| partner_id | bigint | FK to partners (cascade delete) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Primary Key:** Composite (proposal_id, partner_id)

---

#### `proposal_user` (Pivot Table)
**Team member assignments with roles**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| user_id | uuid | FK to users (cascade delete) |
| role | enum | Team role: "ketua" or "anggota" |
| tasks | text | Assigned tasks |
| status | enum | Acceptance status: "pending", "accepted", "rejected" |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `proposals`
- N:1 with `users`

---

#### `proposal_reviewer` (Reviewer Assignments)
**Reviewer assignment and review tracking**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| user_id | uuid | FK to users (cascade delete, reviewer) |
| status | enum | Review status: "pending", "reviewing", "completed" |
| review_notes | text | Reviewer's detailed notes |
| recommendation | enum | Reviewer's recommendation: "approved", "rejected", "revision_needed" |
| created_at | timestamp | Record creation (assignment date) |
| updated_at | timestamp | Last update (review completion) |

**Unique Constraint:** (proposal_id, user_id)

**Relationships:**
- N:1 with `proposals`
- N:1 with `users` (reviewer)

---

### Supporting Tables

#### `keywords` (Kata Kunci)
**Keyword tags for proposals and progress reports**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Keyword text |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- M:N with `proposals` via `proposal_keyword`
- M:N with `progress_reports` via `progress_report_keyword`

---

#### `proposal_keyword` (Pivot Table)
**Many-to-many between proposals and keywords**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| keyword_id | bigint | FK to keywords (cascade delete) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

---

#### `activity_schedules` (Jadwal Kegiatan)
**Project timeline/activity schedule**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| activity_name | string | Activity description |
| year | integer | Year of activity |
| start_month | integer | Start month (1-12) |
| end_month | integer | End month (1-12) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `proposals`

---

#### `research_stages` (Tahapan Penelitian)
**Phased project development with responsible persons**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| stage_number | integer | Sequential stage number |
| process_name | string | Stage/process name |
| outputs | text | Expected stage outputs |
| indicator | text | Success indicators |
| person_in_charge_id | uuid | FK to users (set null) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `proposals`
- N:1 with `users` (person in charge)

---

#### `proposal_outputs` (Luaran yang Direncanakan)
**Planned outputs (publications, patents, etc.)**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| output_year | integer | Target year |
| category | enum | "required" or "additional" |
| type | string | Output type (e.g., "journal", "patent", "book") |
| target_status | string | Target achievement (e.g., "Q1", "Q2", "Granted") |
| output_description | text | Output description |
| journal_name | string | Journal/venue name |
| estimated_date | date | Estimated completion date |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `proposals`

---

### Progress Tracking Tables

#### `progress_reports` (Laporan Kemajuan)
**Semester/annual progress reports**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| proposal_id | uuid | FK to proposals (cascade delete) |
| summary_update | text | Updated project summary |
| reporting_year | integer | Year of report |
| reporting_period | enum | Period: "semester_1", "semester_2", "annual" |
| status | enum | Report status: "draft", "submitted", "approved" |
| submitted_by | uuid | FK to users (submitter) |
| submitted_at | timestamp | Submission timestamp |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Index:** (proposal_id, reporting_year)

**Relationships:**
- N:1 with `proposals`
- N:1 with `users` (submitter)
- 1:N with `mandatory_outputs`
- 1:N with `additional_outputs`
- M:N with `keywords` via `progress_report_keyword`

---

#### `mandatory_outputs` (Luaran Wajib)
**Required outputs reported in progress reports**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| progress_report_id | uuid | FK to progress_reports (cascade delete) |
| output_type | string | Type of output |
| description | text | Output description |
| status | string | Completion status |
| evidence_file | string | Supporting document path |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `progress_reports`

---

#### `additional_outputs` (Luaran Tambahan)
**Extra outputs beyond requirements**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| progress_report_id | uuid | FK to progress_reports (cascade delete) |
| output_type | string | Type of output |
| description | text | Output description |
| status | string | Completion status |
| evidence_file | string | Supporting document path |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `progress_reports`

---

#### `progress_report_keyword` (Pivot Table)
**Keywords for progress reports**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| progress_report_id | uuid | FK to progress_reports (cascade delete) |
| keyword_id | bigint | FK to keywords (cascade delete) |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

---

### Organizational Tables

#### `institutions` (Institusi)
**Universities and organizations**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Institution name |
| type | string | Institution type |
| address | text | Full address |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `study_programs`
- 1:N with `identities`

---

#### `faculties` (Fakultas)
**University faculties**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| code | string | Faculty code (e.g., "SAINTEK", "DEKABITA") |
| name | string | Faculty name |
| description | text | Faculty description |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `study_programs`
- 1:N with `identities`

---

#### `study_programs` (Program Studi)
**Academic programs**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| institution_id | bigint | FK to institutions (cascade delete) |
| faculty_id | bigint | FK to faculties |
| name | string | Program name |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- N:1 with `institutions`
- N:1 with `faculties`
- 1:N with `identities`

---

#### `macro_research_groups` (Kelompok Riset Makro)
**Broad research categorization**

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key (auto-increment) |
| name | string | Group name |
| description | text | Group description |
| created_at | timestamp | Record creation |
| updated_at | timestamp | Last update |

**Relationships:**
- 1:N with `research`

---

### System Tables

#### `notifications`
**Laravel notification storage**

| Column | Type | Description |
|--------|------|-------------|
| id | uuid | Primary key (UUID) |
| type | string | Notification class name |
| notifiable_type | string | Polymorphic: notifiable model type |
| notifiable_id | uuid | Polymorphic: notifiable model ID |
| data | json | Notification payload |
| read_at | timestamp | Read timestamp (nullable) |
| created_at | timestamp | Notification creation |
| updated_at | timestamp | Last update |

**Indexes:**
- (notifiable_type, notifiable_id)
- read_at
- created_at

---

## Relationship Summary

### One-to-One (1:1)
- `users` ↔ `identities`
- `proposals` → `research` (polymorphic morphOne)
- `proposals` → `community_services` (polymorphic morphOne)

### One-to-Many (1:N)
- `users` → `proposals` (submitter)
- `users` → `proposal_reviewer` (reviewer)
- `users` → `research_stages` (person in charge)
- `proposals` → `proposal_outputs`, `budget_items`, `activity_schedules`, `research_stages`, `progress_reports`, `proposal_reviewer`
- `focus_areas` → `themes`, `proposals`
- `themes` → `topics`, `proposals`
- `topics` → `proposals`
- `research_schemes` → `proposals`
- `national_priorities` → `proposals`
- `science_clusters` → `science_clusters` (self-referencing)
- `science_clusters` → `proposals` (3 separate FKs)
- `budget_groups` → `budget_components`, `budget_items`
- `budget_components` → `budget_items`
- `partners` → `community_services` (main partner)
- `progress_reports` → `mandatory_outputs`, `additional_outputs`
- `institutions` → `study_programs`, `identities`
- `faculties` → `study_programs`, `identities`
- `study_programs` → `identities`
- `macro_research_groups` → `research`

### Many-to-Many (M:N)
- `proposals` ↔ `users` via `proposal_user` (team members)
- `proposals` ↔ `keywords` via `proposal_keyword`
- `proposals` ↔ `partners` via `proposal_partner`
- `progress_reports` ↔ `keywords` via `progress_report_keyword`

### Polymorphic
- `proposals.detailable` → `research` OR `community_services` (type: detailable_type, id: detailable_id)
- `notifications.notifiable` → `users` (type: notifiable_type, id: notifiable_id)

---

## Plain-English Summary

The SIM LPPM database is designed around a central **proposals** table that supports two distinct proposal types: **Research** (Penelitian) and **Community Service** (PKM/Pengabdian) through a polymorphic relationship. 

**Research proposals** require scientific methodology, TKT (Technology Readiness Level) tracking, state-of-the-art literature review, and multi-year roadmap planning stored as JSON. They are categorized by macro research groups and focus on academic outputs like publications and patents.

**PKM proposals** focus on community problem-solving, requiring partner organization information, problem summaries, and solution descriptions. They emphasize social impact over academic contributions.

Both proposal types share common infrastructure:
- **Team management** through `proposal_user` pivot table with role assignments (ketua/anggota) and acceptance tracking
- **Budget planning** using a 2-level hierarchy (Groups → Components) with line items tracking volume, unit price, and calculated totals
- **Activity scheduling** with year, start month, and end month tracking
- **Proposal outputs** planning with categories (required/additional), types, and target status
- **Research stages** with responsible persons and success indicators

The system implements a **3-level taxonomy** (Focus Areas → Themes → Topics) and a **3-level science cluster** classification for detailed categorization. Proposals are further classified by research schemes, national priorities (PRN alignment), and linked to master data like keywords, partners, and institutions.

**Progress tracking** is comprehensive with `progress_reports` supporting semester and annual reporting, linked to mandatory and additional outputs with evidence files. Keywords can be associated with both proposals and progress reports for searchability.

**Organizational structure** links users to institutions, faculties, and study programs through the `identities` table, enabling faculty-scoped permissions for deans and institutional reporting.

**Review workflow** is managed through `proposal_reviewer` table tracking assignment, review status, notes, and recommendations. The status field on proposals uses an enum with 9 states tracking the complete lifecycle from draft to completion or rejection.

All core entities use **UUID primary keys** for security and distributed system compatibility, while reference/master data tables use auto-increment integers for performance. Foreign key constraints ensure referential integrity with appropriate cascade rules (cascade delete for dependent data, set null for optional references).

The design separates concerns effectively: core proposal data in `proposals`, type-specific data in `research`/`community_services`, collaboration data in pivot tables, master/reference data in dedicated tables, and progress tracking in report tables. This enables flexible querying, maintains data integrity, and supports the complex multi-stage approval workflow central to the system.

---

**Document End**
