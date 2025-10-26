# Dokumentasi ERD Skema Database SIM-LPPM

## Gambaran Sistem
Ini adalah Diagram Entitas-Hubungan (ERD) untuk SIM-LPPM (Sistem Informasi Lembaga Penelitian dan Pengabdian kepada Masyarakat) - sistem manajemen penelitian akademik dan pengabdian kepada masyarakat yang dibangun dengan Laravel 12.

## Legenda Tipe Entitas
- **Entitas Kuat**: Objek domain utama (Users, Proposals, dll.)
- **Entitas Lemah**: Entitas dependen (ResearchStages, BudgetItems, dll.)
- **Entitas Referensi**: Tabel lookup/enumerasi (FocusArea, Theme, dll.)
- **Entitas Pivot**: Tabel hubungan many-to-many (proposal_user, proposal_keyword)
- **Relasi Polymorphic**: Tipe hubungan dinamis (Proposal → Research/CommunityService)

---

## Entitas Inti & Hubungan

### Sistem Manajemen Pengguna
```
Users (Strong Entity)
├── id (PK)
├── name
├── email (unique)
├── password (hashed)
├── email_verified_at
├── two_factor_secret
├── two_factor_recovery_codes
├── remember_token
└── created_at/updated_at

Users 1:N Submissions
Users M:N Team Membership (via proposal_user pivot)
Users 1:N Research Stages (person_in_charge)

Users 1:1 Identity (extended profile)
Users M:N Roles/Permissions (via spatie/permission)
```

### Sistem Proposal (Entitas Sentral)
```
Proposals (Strong Entity)
├── id (PK)
├── title
├── submitter_id (FK → users.id)
├── detailable_type/id (Polymorphic → research/community_services)
├── research_scheme_id (FK → research_schemes.id)
├── focus_area_id (FK → focus_areas.id)
├── theme_id (FK → themes.id)
├── topic_id (FK → topics.id)
├── national_priority_id (FK → national_priorities.id)
├── cluster_level1_id (FK → science_clusters.id)
├── cluster_level2_id (FK → science_clusters.id)
├── cluster_level3_id (FK → science_clusters.id)
├── sbk_value (decimal)
├── duration_in_years (integer)
├── summary (text)
├── status (enum: draft/submitted/reviewed/approved/rejected/completed)
└── created_at/updated_at

Proposals 1:1 Polymorphic (detailable → Research | CommunityService)
Proposals 1:N Outputs (ProposalOutput)
Proposals 1:N Budget Items (BudgetItem)
Proposals 1:N Activity Schedules (ActivitySchedule)
Proposals 1:N Research Stages (ResearchStage)
Proposals M:N Team Members (via proposal_user pivot)
Proposals M:N Keywords (via proposal_keyword pivot)
```

### Entitas Research (Ekstensi Polymorphic)
```
Research
├── id (PK)
├── final_tkt_target (nullable)
├── background (longText)
├── state_of_the_art (longText)
├── methodology (longText)
├── roadmap_data (JSON)
└── created_at/updated_at

Research 1:1 Proposal (morphOne, detailable_type='App\Models\Research')
```

### Entitas Community Service (Ekstensi Polymorphic)
```
CommunityServices
├── id (PK)
├── partner_id (FK → partners.id, nullable)
├── partner_issue_summary (text, nullable)
├── solution_offered (text, nullable)
└── created_at/updated_at

CommunityServices 1:1 Proposal (morphOne, detailable_type='App\Models\CommunityService')
CommunityServices N:1 Partner
```

### Partners (Organisasi Eksternal)
```
Partners
├── id (PK)
├── name
├── type
├── address
└── created_at/updated_at

Partners 1:N CommunityServices
```

---

## Sistem Taksonomi Hierarkis

### Focus Areas → Themes → Topics
```
FocusAreas (Reference Entity)
├── id (PK)
├── name
└── created_at/updated_at

FocusAreas 1:N Themes
FocusAreas 1:N Proposals (direct relationship)

Themes (Reference Entity)
├── id (PK)
├── focus_area_id (FK → focus_areas.id, cascade delete)
├── name
└── created_at/updated_at

Themes N:1 FocusArea
Themes 1:N Topics
Themes 1:N Proposals (direct relationship)

Topics (Reference Entity)
├── id (PK)
├── theme_id (FK → themes.id, cascade delete)
├── name
└── created_at/updated_at

Topics N:1 Theme
Topics 1:N Proposals (direct relationship)
```

### Other Reference Tables
```
ResearchSchemes
├── id (PK)
├── name
├── description (nullable)
└── created_at/updated_at
ResearchSchemes 1:N Proposals

NationalPriorities
├── id (PK)
├── name
├── description (nullable)
└── created_at/updated_at
NationalPriorities 1:N Proposals

ScienceClusters (3-level hierarchy)
├── id (PK)
├── name
├── level (1/2/3)
├── parent_id (FK → science_clusters.id, nullable, for levels 2-3)
└── created_at/updated_at
ScienceClusters 1:N Proposals (3 separate FKs for each level)

StudyPrograms
├── id (PK)
├── name
├── description (nullable)
└── created_at/updated_at

Keywords
├── id (PK)
├── name
└── created_at/updated_at
Keywords M:N Proposals (via proposal_keyword pivot)
```

---

## Supporting Entities

### Proposal Outputs (Expected Results)
```
ProposalOutputs
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── output_year
├── category (required/additional)
├── type (journal/patent/etc)
├── target_status (Q1/Q2/Granted/etc)
└── created_at/updated_at

ProposalOutputs N:1 Proposal
```

### Budget Items (Financial Planning)
```
BudgetItems
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── group (honor/equipment/etc)
├── component
├── item_description
├── volume (integer)
├── unit_price (decimal)
├── total_price (decimal, calculated)
└── created_at/updated_at

BudgetItems N:1 Proposal
```

### Activity Schedules (Project Timeline)
```
ActivitySchedules
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── activity_name
├── year
├── start_month (1-12)
├── end_month (1-12)
└── created_at/updated_at

ActivitySchedules N:1 Proposal
```

### Research Stages (Phased Development)
```
ResearchStages
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── stage_number
├── process_name
├── outputs (text, nullable)
├── indicator (text, nullable)
├── person_in_charge_id (FK → users.id, set null)
└── created_at/updated_at

ResearchStages N:1 Proposal
ResearchStages N:1 User (person_in_charge, nullable)
```

---

## Many-to-Many Relationships (Pivot Tables)

### Team Membership
```
proposal_user (Pivot Table)
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── user_id (FK → users.id, cascade delete)
├── role (enum: ketua/anggota)
├── tasks (text, nullable)
└── created_at/updated_at
```

### Keyword Tagging
```
proposal_keyword (Pivot Table)
├── id (PK)
├── proposal_id (FK → proposals.id, cascade delete)
├── keyword_id (FK → keywords.id, cascade delete)
└── created_at/updated_at
```

---

## User Identity Extension
```
Identities
├── id (PK)
├── user_id (FK → users.id)
├── profile_picture (nullable)
├── other identity fields...
└── created_at/updated_at

Identities 1:1 User
```

---

## Additional System Tables

### Laravel Core Tables
- **users**: Authentication (analyzed above)
- **cache**: Laravel caching
- **jobs**: Queue system
- **sessions**: Session management

### Laravel Telescope (Development Monitoring)
- **telescope_entries**: Request/debugging logs

### Spatie Laravel Permission
- **roles, permissions, model_has_permissions, etc.**: Role-based access control

---

## Relationship Summary

### One-to-One (1:1)
- User ↔ Identity
- Proposal → Research (polymorphic)
- Proposal → CommunityService (polymorphic)

### One-to-Many (1:N)
- User → Submitted Proposals
- User → Research Stages (person in charge)
- Proposal → Outputs, Budget Items, Activity Schedules, Research Stages
- FocusArea → Themes, Proposals
- Theme → Topics, Proposals
- Topic → Proposals
- Partner → CommunityServices
- Various Reference Tables → Proposals

### Many-to-Many (M:N)
- Users ↔ Proposals (team membership via proposal_user)
- Keywords ↔ Proposals (tagging via proposal_keyword)

### Polymorphic (Dynamic)
- Proposals → Research/CommunityService (single table inheritance pattern)

---

## Catatan Logika Bisnis

1. **Tipe Proposal**: Setiap proposal adalah baik Research ATAU Community Service (saling eksklusif)
2. **Struktur Tim**: Setiap proposal memiliki satu pengaju dan beberapa anggota tim dengan peran (ketua/anggota) dan tugas yang ditugaskan
3. **Hierarki Taksonomi**: Focus Areas berisi Themes, Themes berisi Topics, memungkinkan kategorisasi granular
4. **Pengelompokan Ilmu**: Sistem klasifikasi ilmu tiga level untuk kategorisasi detail
5. **Siklus Proyek**: Pelacakan komprehensif melalui draf, ulasan, persetujuan, dan penyelesaian
6. **Manajemen Keuangan**: Rincian anggaran terperinci berdasarkan komponen dan item
7. **Perencanaan Output**: Perencanaan output tahunan dengan kategori dan target pencapaian
8. **Metodologi Penelitian**: Pendekatan bertahap dengan tanggung jawab dan indikator kesuksesan yang ditugaskan

---

## Integritas Data & Kendala

### Kendala Foreign Key
- Semua FK menggunakan aturan cascade/delete yang sesuai
- Relasi polymorphic diindeks dengan benar
- Kendala unik pada field kritis (email pengguna)

### Kendala Enum
- Penegakan alur kerja status proposal
- Validasi peran anggota tim
- Validasi kategori dan tipe output

### Aturan Bisnis
- Proposal penelitian tidak dapat memiliki mitra pengabdian masyarakat (dan sebaliknya)
- Hierarki taksonomi dijaga melalui relasi FK
- Nilai SBK dihitung berdasarkan parameter proposal
- Kendala timeline (jadwal kegiatan, tahapan penelitian)
