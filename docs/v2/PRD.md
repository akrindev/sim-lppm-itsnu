# Product Requirements Document (PRD) v2.0
## SIM LPPM ITSNU - Research & Community Service Management System

**Document Version:** 2.0  
**Last Updated:** 2025-11-09  
**Product Owner:** LPPM ITSNU Pekalongan  
**Development Team:** Internal IT Team

---

## 1. Executive Summary

**SIM LPPM ITSNU** is a comprehensive web-based management system designed to digitize and streamline the entire lifecycle of research grants (Penelitian) and community service programs (Pengabdian kepada Masyarakat/PKM) at Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan.

The platform transforms manual, paper-based grant management into an automated, transparent, and accountable digital workflow. Inspired by the national BIMA Kemendikbud system, SIM LPPM is tailored specifically for ITSNU's institutional needs, supporting multiple stakeholder roles from proposal submission through final reporting.

**Key Value Proposition:**
- **Efficiency:** Reduce proposal processing time from weeks to days
- **Transparency:** Real-time status tracking for all stakeholders
- **Accountability:** Complete audit trail of approvals and reviews
- **Accessibility:** Web-based 24/7 access for faculty and staff

---

## 2. Problem Statement

### Current Challenges

**2.1 Manual Proposal Management**
- Paper-based submissions create bottlenecks and loss risks
- Tracking proposal status requires manual follow-up
- Document version control is difficult
- Physical filing systems are space-intensive and hard to search

**2.2 Inefficient Review Process**
- Assigning reviewers manually is time-consuming
- No standardized review workflow
- Difficult to track review completion status
- Feedback collection is fragmented

**2.3 Limited Progress Monitoring**
- No systematic way to track ongoing projects
- Progress reports submitted manually without validation
- Difficult to identify at-risk projects early
- No centralized repository of project outputs

**2.4 Poor Data Accessibility**
- Historical data is scattered across physical files
- Generating reports for accreditation is labor-intensive
- Decision-makers lack real-time analytics
- No institution-wide visibility into research activities

**2.5 Lack of Collaboration Tools**
- Team coordination happens outside the system
- Budget planning is done in separate spreadsheets
- No integrated notification system
- Partner management for PKM is informal

---

## 3. Goals & Objectives

### Primary Goals

**G1: Digitize Complete Grant Lifecycle**
- Automate proposal submission to final report
- Eliminate paper-based processes
- Create single source of truth for all grant data

**G2: Streamline Multi-Role Approval Workflow**
- Implement structured approval chain (Dekan → Kepala LPPM → Reviewers)
- Enable parallel review processes
- Support revision and resubmission cycles

**G3: Ensure Transparency & Accountability**
- Real-time status visibility for all stakeholders
- Complete audit trail of all actions
- Notification system for timely updates

**G4: Enable Data-Driven Decisions**
- Centralized reporting and analytics
- Track institutional research trends
- Support accreditation requirements

### Secondary Objectives

**O1: Improve User Experience**
- Intuitive interfaces for all user roles
- Mobile-responsive design
- Contextual help and guidance

**O2: Reduce Administrative Burden**
- Automate repetitive tasks (notifications, status updates)
- Standardize data entry with master data
- Minimize manual data re-entry

**O3: Support Collaboration**
- Team member invitation and management
- Partner organization integration (PKM)
- Reviewer coordination

**O4: Maintain Data Quality**
- Validation rules for all inputs
- Hierarchical taxonomy for categorization
- Controlled vocabularies (keywords, schemes)

---

## 4. Core Features

### 4.1 Research Module (Penelitian)

**Proposal Submission**
- Multi-step form with validation
- Scientific methodology documentation
- TKT (Technology Readiness Level) tracking
- Multi-year roadmap planning (JSON structure)
- Literature review (state-of-the-art)
- Macro research group categorization

**Research-Specific Data:**
- Background and problem statement
- Methodology and approach
- Expected outputs (publications, patents, prototypes)
- Roadmap with annual targets
- TKT progression plan (0-9 scale)

### 4.2 Community Service Module (PKM/Pengabdian)

**Proposal Submission**
- Community problem identification
- Solution design documentation
- Partner organization management
- Social impact planning

**PKM-Specific Data:**
- Partner issue summary
- Solution offered
- Community engagement plan
- Expected social impact
- Partner collaboration details

### 4.3 Multi-Stage Approval System

**Approval Workflow:**
1. **Team Acceptance:** All team members must accept invitation before submission
2. **Submission:** Dosen submits proposal (status: SUBMITTED)
3. **Dekan Approval:** Faculty dean reviews and approves (status: APPROVED)
4. **Kepala LPPM Initial Approval:** LPPM director approves for review (status: UNDER_REVIEW)
5. **Reviewer Assignment:** Admin LPPM assigns expert reviewers
6. **Review Process:** Reviewers evaluate and recommend (status: REVIEWED)
7. **Kepala LPPM Final Decision:** Final approval or revision request (status: COMPLETED/REVISION_NEEDED)

**Features:**
- Status transitions with validation rules
- Role-based approval permissions
- Notification triggers at each stage
- Rejection and revision pathways
- Complete audit trail

### 4.4 Review & Revision Workflow

**Reviewer Features:**
- Access to full proposal details
- Structured evaluation form
- Recommendation options (approved/revision/rejected)
- Review notes and comments
- Status tracking (pending/reviewing/completed)

**Revision Process:**
- Proposals marked for revision return to Dosen
- Track revision history
- Re-enter approval workflow after resubmission
- Compare versions (future enhancement)

### 4.5 Team & Budget Management

**Team Collaboration:**
- Invite team members (ketua/anggota roles)
- Track acceptance status (pending/accepted/rejected)
- Assign specific tasks to members
- Team member profile visibility

**Budget Planning:**
- 2-level budget hierarchy (Groups → Components)
- Volume-based calculations (volume × unit_price = total)
- Budget group categorization (Honor, Equipment, Travel, etc.)
- SBK (Satuan Biaya Keluaran) validation
- Budget summary and reporting

### 4.6 Progress Reporting System

**Report Types:**
- Semester 1 reports
- Semester 2 reports
- Annual reports
- Final reports

**Report Contents:**
- Summary updates
- Mandatory outputs (required deliverables)
- Additional outputs (extra achievements)
- Keywords for tracking
- Status tracking (draft/submitted/approved)

### 4.7 Notification System

**Notification Channels:**
- In-app database notifications
- Email notifications (queued for async delivery)

**Notification Types:**
- Proposal submission alerts
- Review assignments
- Team invitations
- Approval decisions
- Review completion
- Scheduled reminders (review deadlines)
- Daily/weekly summaries for administrators

**UI Components:**
- Notification dropdown (top navbar)
- Notification center (dedicated page)
- Mark as read functionality
- Pagination and filtering

### 4.8 Role-Based Access Control (RBAC)

**9 System Roles:**
1. **Superadmin:** Full system access (IT administrators)
2. **Admin LPPM:** Reviewer assignment, master data management, user administration
3. **Kepala LPPM:** Strategic oversight, initial/final approvals
4. **Dekan:** Faculty-level first approval
5. **Dekan Saintek:** Science faculty proposals
6. **Dekan Dekabita:** Social science faculty proposals
7. **Dosen:** Proposal creation, submission, progress reporting
8. **Reviewer:** Expert evaluation and recommendations
9. **Rektor:** Strategic approval for high-priority proposals

**Permission System:**
- Spatie Laravel Permission package
- Role-based access to routes and components
- Data scope filtering (faculty-level, own proposals, assigned reviews)
- Action-based permissions (create, view, approve, assign, etc.)

### 4.9 Master Data Management

**Taxonomy Management:**
- Focus Areas → Themes → Topics (3-level hierarchy)
- Science Clusters (3-level: Bidang → Subbidang → Detail)
- Research Schemes (e.g., Basic, Applied, Development)
- National Priorities (PRN alignment)

**Reference Data:**
- Keywords (tagging system)
- Partners (for PKM proposals)
- Budget Groups & Components
- Faculties & Study Programs
- Institutions
- Macro Research Groups

**Management Interface:**
- CRUD operations for all master data
- Restricted to Admin LPPM and Kepala LPPM
- Hierarchical data relationships maintained
- Used for controlled vocabularies in proposals

### 4.10 Reporting & Analytics

**Dashboard Views:**
- Role-specific dashboards
- Proposal status distribution
- Review progress tracking
- Faculty-level statistics
- Personal proposal tracking (Dosen)

**Reports:**
- Proposal list with filters (status, type, faculty)
- Review summary reports
- Progress report compilation
- Output tracking
- Budget summaries

---

## 5. User Stories

### 5.1 Dosen (Lecturer)

**US-D1:** As a Dosen, I can create a new research proposal by filling in all required fields, so that I can submit my research idea for review.

**US-D2:** As a Dosen, I can invite team members to collaborate on my proposal, so that we can work together on the project.

**US-D3:** As a Dosen, I can submit my proposal once all team members have accepted their invitations, so that it proceeds to the approval workflow.

**US-D4:** As a Dosen, I can track the real-time status of my proposal, so that I know where it is in the approval process.

**US-D5:** As a Dosen, I can revise and resubmit my proposal if it is marked for revision, so that I can address reviewer feedback.

**US-D6:** As a Dosen, I can submit progress reports (semester/annual) for approved proposals, so that I fulfill my reporting obligations.

### 5.2 Reviewer

**US-R1:** As a Reviewer, I receive notifications when I am assigned to review a proposal, so that I know I have work to do.

**US-R2:** As a Reviewer, I can access the full proposal details including methodology and budget, so that I can make an informed evaluation.

**US-R3:** As a Reviewer, I can submit my review with notes and recommendations (approved/revision/rejected), so that my expertise contributes to the decision.

**US-R4:** As a Reviewer, I can see the list of all proposals assigned to me, so that I can prioritize my review work.

### 5.3 Admin LPPM

**US-A1:** As an Admin LPPM, I can assign appropriate reviewers to proposals after Kepala LPPM approval, so that expert evaluation can begin.

**US-A2:** As an Admin LPPM, I can manage master data (focus areas, themes, keywords, partners), so that Dosen have accurate reference data for proposals.

**US-A3:** As an Admin LPPM, I can create new user accounts and assign roles, so that faculty and staff can access the system.

**US-A4:** As an Admin LPPM, I receive daily summary reports of system activity, so that I can monitor operations.

### 5.4 Kepala LPPM (LPPM Director)

**US-K1:** As a Kepala LPPM, I can review proposals that have been approved by Dekan and give initial approval, so that they proceed to the review stage.

**US-K2:** As a Kepala LPPM, I can make final decisions on proposals after all reviewers have completed their evaluations, so that proposals can be approved or marked for revision.

**US-K3:** As a Kepala LPPM, I can view aggregated reports of all proposals across all faculties, so that I have strategic oversight.

**US-K4:** As a Kepala LPPM, I receive weekly summary reports, so that I stay informed about LPPM activities.

### 5.5 Dekan (Dean)

**US-DE1:** As a Dekan, I can review proposals submitted by Dosen in my faculty, so that I can approve quality research aligned with faculty goals.

**US-DE2:** As a Dekan, I can request team member fixes if there are acceptance issues, so that proposals have proper team composition before proceeding.

**US-DE3:** As a Dekan, I can view faculty-level dashboard showing all proposals from my faculty, so that I can track faculty research activity.

### 5.6 Team Member (Anggota)

**US-T1:** As a Team Member, I receive an invitation to join a proposal, so that I can decide whether to participate.

**US-T2:** As a Team Member, I can accept or reject team invitations, so that I only commit to projects I can contribute to.

**US-T3:** As a Team Member, I receive notifications when the proposal I'm part of has status updates, so that I stay informed.

### 5.7 Rektor

**US-RE1:** As a Rektor, I can view all proposals across the university, so that I have institutional visibility.

**US-RE2:** As a Rektor, I can approve strategic high-priority proposals, so that the university prioritizes important research.

---

## 6. Non-Goals (Out of Scope)

### What This System Does NOT Do:

**NG1: External API Integration**
- Not integrated with national BIMA Kemendikbud system
- No third-party API for external organizations
- Internal-only system for ITSNU

**NG2: Financial Transaction Processing**
- System tracks budget planning only
- Does not process actual payments or disbursements
- No integration with accounting/financial systems
- Budget data is exported for finance department

**NG3: Mobile Native Applications**
- Web-responsive design only
- Not a native iOS/Android app
- Accessible via mobile browsers

**NG4: Document Generation**
- System stores data, doesn't auto-generate Word/PDF proposal documents
- Reports are HTML/printable, not formatted templates
- Users prepare documents externally, upload to system

**NG5: Plagiarism Checking**
- No integrated plagiarism detection
- Users responsible for originality checking

**NG6: Grant Funding Distribution**
- System manages proposals, not actual funding allocation
- Financial disbursement handled by finance department

**NG7: External Researcher Collaboration**
- System is for ITSNU internal users only
- External collaborators managed informally
- No external user accounts

**NG8: Intellectual Property Management**
- Basic output tracking only (publications, patents)
- No IP rights management or licensing
- Legal aspects handled separately

---

## 7. Technical Stack

### Backend
- **Framework:** Laravel 12
- **PHP Version:** 8.4
- **Authentication:** Laravel Fortify (with 2FA support)
- **Permissions:** Spatie Laravel Permission
- **Queue System:** Laravel Queues (for notifications)
- **Testing:** Pest v4

### Frontend
- **Framework:** Livewire 3 (full-stack reactivity)
- **UI Library:** Tabler (Bootstrap 5-based admin template)
- **Icons:** Lucide Icons
- **Build Tool:** Vite
- **CSS:** Tailwind CSS v4 + Bootstrap 5

### Database
- **RDBMS:** MySQL
- **Key Strategy:** UUIDs for primary keys (security, distributed systems)
- **ORM:** Eloquent

### Infrastructure
- **Web Server:** Apache/Nginx
- **Deployment:** Manual deployment (no CI/CD yet)
- **Monitoring:** Laravel Telescope (development)

### Development Tools
- **Code Style:** Laravel Pint
- **Version Control:** Git
- **Package Manager:** Composer (PHP), Bun (JavaScript)

---

## 8. Success Metrics

### Operational Metrics
- **Proposal Processing Time:** Reduce from 4-6 weeks to 2-3 weeks
- **System Uptime:** 99%+ availability during business hours
- **User Adoption:** 80%+ of faculty using system within 6 months

### Quality Metrics
- **Data Accuracy:** <1% data entry errors
- **Review Completion:** 90%+ reviews completed within 14 days
- **User Satisfaction:** 4/5+ rating in user surveys

### Impact Metrics
- **Research Output:** Track year-over-year increase in proposals
- **Transparency:** 100% of proposals have visible status
- **Reporting Efficiency:** Reduce accreditation report prep time by 70%

---

## 9. Assumptions & Constraints

### Assumptions
- Faculty have basic computer literacy
- Internet access available at all times
- Users have valid email addresses for notifications
- Institutional data (faculties, study programs) is stable

### Constraints
- Budget: Limited to internal development resources
- Timeline: Iterative development, no hard launch deadline
- Infrastructure: Hosted on-premises (university servers)
- Support: Limited IT support staff (1-2 persons)

---

## 10. Future Enhancements (Roadmap)

**Phase 2 (6-12 months):**
- Document version comparison
- Advanced analytics dashboard
- API for integration with institutional systems
- Mobile-optimized interface improvements

**Phase 3 (12-18 months):**
- AI-powered proposal similarity detection
- Automated reviewer recommendation
- Grant funding integration with finance system
- Public proposal database (approved proposals)

**Phase 4 (18-24 months):**
- Inter-institutional collaboration features
- External researcher portal
- Publication tracking integration (Scopus, Google Scholar)
- Automated report generation

---

## Document Approval

| Role | Name | Signature | Date |
|------|------|-----------|------|
| Product Owner | LPPM ITSNU | ___________ | ___________ |
| Technical Lead | IT Team | ___________ | ___________ |
| Stakeholder Representative | Kepala LPPM | ___________ | ___________ |

---

**Document End**
