# SIM LPPM ITSNU - Documentation v2.0

**Complete System Documentation**  
**Last Updated:** 2025-11-09  
**Total Documents:** 8

---

## 📚 Documentation Index

### 1. [PRD.md](./PRD.md) - Product Requirements Document
**Size:** 17 KB | **Pages:** ~8

**Overview:**
- Executive summary and problem statement
- Goals, objectives, and success metrics
- Core features (Research & PKM modules)
- User stories for all 9 roles
- Non-goals and technical stack
- Future roadmap

**Target Audience:** Product owners, stakeholders, project managers

---

### 2. [ERD.md](./ERD.md) - Entity-Relationship Diagram
**Size:** 30 KB | **Pages:** ~15

**Overview:**
- Complete Mermaid ERD (45+ custom tables)
- Detailed table descriptions with columns, types, constraints
- All relationships (1:1, 1:N, M:N, polymorphic)
- Plain-English summary
- Excludes Laravel default tables

**Target Audience:** Developers, database administrators, technical architects

---

### 3. [WORKFLOWS.md](./WORKFLOWS.md) - Complete Process Flows
**Size:** 29 KB | **Pages:** ~14

**Overview:**
- Complete proposal lifecycle diagram
- Workflows by role (Dosen, Dekan, Kepala LPPM, Admin, Reviewer)
- Sequence diagrams for each workflow
- Corrected approval sequence with notifications
- Executive summary with timelines

**Target Audience:** All users, process analysts, trainers

---

### 4. [ROLES.md](./ROLES.md) - Roles & Permissions Matrix
**Size:** 24 KB | **Pages:** ~12

**Overview:**
- 9 system roles with descriptions
- Complete permissions matrix (50+ permissions)
- Data scope and access levels
- Role hierarchy and authorization examples
- Implementation details (Spatie Permission)

**Target Audience:** System administrators, security auditors, developers

---

### 5. [NOTIFICATIONS.md](./NOTIFICATIONS.md) - Notification System
**Size:** 3.9 KB | **Pages:** ~2 (Concise)

**Overview:**
- 13 notification types with triggers and recipients
- Database payload structure
- UI components (dropdown, notification center)
- Email template structure
- Notification flow diagram

**Target Audience:** Developers, UX designers, system administrators

---

### 6. [STATUS-TRANSITIONS.md](./STATUS-TRANSITIONS.md) - Status Lifecycle
**Size:** 12 KB | **Pages:** ~6

**Overview:**
- 9 ProposalStatus enum states
- State transition diagram (Mermaid)
- Valid transitions matrix with business rules
- Status descriptions and durations
- Validation implementation examples

**Target Audience:** Developers, business analysts, testers

---

### 7. [DATA-STRUCTURES.md](./DATA-STRUCTURES.md) - Research vs PKM
**Size:** 15 KB | **Pages:** ~7

**Overview:**
- Polymorphic relationship explanation
- Research-specific data (TKT, roadmap, methodology)
- PKM-specific data (partner, issue, solution)
- Comparison table (10+ aspects)
- Common proposal data (shared structures)
- UI and validation differences

**Target Audience:** Developers, content creators, proposal reviewers

---

### 8. [MASTER-DATA.md](./MASTER-DATA.md) - Taxonomy & Reference Data
**Size:** 19 KB | **Pages:** ~9

**Overview:**
- 3-level taxonomy (Focus Areas → Themes → Topics)
- 3-level science clusters
- Research classification (schemes, priorities, groups)
- 2-level budget hierarchy (Groups → Components)
- Partners, keywords, organizational structure
- Master data management UI and access control

**Target Audience:** Admin LPPM, content managers, data stewards

---

## 📊 Documentation Statistics

| Document | Size | Lines | Diagrams | Tables |
|----------|------|-------|----------|--------|
| PRD.md | 17 KB | 650 | 0 | 5 |
| ERD.md | 30 KB | 1,100 | 1 (large) | 40+ |
| WORKFLOWS.md | 29 KB | 1,050 | 12 | 8 |
| ROLES.md | 24 KB | 900 | 1 | 12 |
| NOTIFICATIONS.md | 3.9 KB | 150 | 1 | 1 |
| STATUS-TRANSITIONS.md | 12 KB | 450 | 2 | 3 |
| DATA-STRUCTURES.md | 15 KB | 600 | 2 | 6 |
| MASTER-DATA.md | 19 KB | 750 | 2 | 15 |
| **TOTAL** | **160 KB** | **5,650** | **21** | **90+** |

---

## 🎯 Quick Start by Role

### For Developers
1. Start with **ERD.md** (understand database structure)
2. Read **DATA-STRUCTURES.md** (Research vs PKM)
3. Review **STATUS-TRANSITIONS.md** (business logic)
4. Check **WORKFLOWS.md** (user interactions)

### For Product Owners / Managers
1. Start with **PRD.md** (business context)
2. Read **WORKFLOWS.md** (user journeys)
3. Review **ROLES.md** (permissions)
4. Check **STATUS-TRANSITIONS.md** (approval flow)

### For Administrators
1. Start with **ROLES.md** (access control)
2. Read **MASTER-DATA.md** (data management)
3. Review **NOTIFICATIONS.md** (communication)
4. Check **WORKFLOWS.md** (operations)

### For End Users (Dosen, Reviewer, Dekan)
1. Start with **WORKFLOWS.md** (your workflows)
2. Read relevant sections in **PRD.md** (features)
3. Review **STATUS-TRANSITIONS.md** (proposal lifecycle)
4. Check **ROLES.md** (your permissions)

---

## 🔄 Document Relationships

```
PRD.md (Business Requirements)
  ↓
ERD.md (Data Model)
  ↓
WORKFLOWS.md (Process Flows)
  ↓
STATUS-TRANSITIONS.md (State Machine)
  ↓
ROLES.md (Permissions)
  ├→ NOTIFICATIONS.md (Communication)
  ├→ DATA-STRUCTURES.md (Content Types)
  └→ MASTER-DATA.md (Reference Data)
```

---

## ✅ Key Corrections in v2

### Workflow Correction
**v1 (Incorrect):**
```
Submit → Dekan → Admin assigns reviewers → Kepala LPPM approval
```

**v2 (Correct):**
```
Team Accept → Submit → Dekan → Kepala LPPM Initial → Admin assigns reviewers → Reviews → Kepala LPPM Final
```

### Key Changes
1. ✅ Team acceptance BEFORE submission (not after Dekan)
2. ✅ Kepala LPPM has TWO approvals (initial + final)
3. ✅ Admin assigns reviewers AFTER Kepala LPPM initial approval
4. ✅ NEED_ASSIGNMENT status for team issues
5. ✅ UNDER_REVIEW and REVIEWED as separate states

---

## 📝 Document Conventions

### Terminology
- **Research** = Penelitian (scientific research)
- **PKM** = Pengabdian kepada Masyarakat (community service)
- **Dosen** = Lecturer/Researcher
- **Dekan** = Faculty Dean
- **Kepala LPPM** = LPPM Director
- **Admin LPPM** = LPPM Administrator
- **Reviewer** = Expert Evaluator

### Status Values
- Always use `ProposalStatus` enum constants
- 9 states: DRAFT, SUBMITTED, NEED_ASSIGNMENT, APPROVED, UNDER_REVIEW, REVIEWED, REVISION_NEEDED, COMPLETED, REJECTED

### Role Names
- Exact match with database: `'dosen'`, `'admin lppm'`, `'kepala lppm'`, `'dekan'`, `'reviewer'`, etc.
- Case-sensitive in code

---

## 🔗 External References

- **Codebase:** `/home/salju/Workspace/sim-lppm-itsnu/`
- **Main README:** `../README.md`
- **Technical Guidelines:** `../CLAUDE.md`
- **Old Documentation:** `../database/*.md` (deprecated)

---

## 📞 Maintenance

**Document Owner:** Development Team  
**Review Cycle:** Quarterly or after major changes  
**Feedback:** Submit issues via project repository

---

**Last Review:** 2025-11-09  
**Next Review:** 2026-02-09  
**Status:** ✅ Complete and Approved
