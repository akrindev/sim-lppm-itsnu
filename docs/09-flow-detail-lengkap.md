# Flow Detail Lengkap SIM LPPM ITSNU

Dokumentasi ini menjelaskan alur kerja sistem secara mendetail, mencakup **Role**, **Status**, **Page/URL**, **Component Livewire**, dan **Action** yang terlibat di setiap tahap.

---

## Daftar Isi

1. [Legenda Status](#legenda-status)
2. [Daftar Role](#daftar-role)
3. [Flow Diagram Utama](#flow-diagram-utama)
4. [Tahap 1: Penyusunan Proposal (Dosen)](#tahap-1-penyusunan-proposal-dosen)
5. [Tahap 2: Validasi Dekan](#tahap-2-validasi-dekan)
6. [Tahap 3: Persetujuan Awal Kepala LPPM](#tahap-3-persetujuan-awal-kepala-lppm)
7. [Tahap 4: Penugasan Reviewer (Admin LPPM)](#tahap-4-penugasan-reviewer-admin-lppm)
8. [Tahap 5: Proses Review (Reviewer)](#tahap-5-proses-review-reviewer)
9. [Tahap 6: Keputusan Final (Kepala LPPM)](#tahap-6-keputusan-final-kepala-lppm)
10. [Tahap 7: Siklus Revisi (Loop)](#tahap-7-siklus-revisi-loop)
11. [Transisi Status Lengkap](#transisi-status-lengkap)
12. [Mapping Page per Role](#mapping-page-per-role)

---

## Legenda Status

### Status Proposal (`ProposalStatus`)

| Status | Label (ID) | Warna | Deskripsi |
|--------|------------|-------|-----------|
| `DRAFT` | Draft | secondary | Proposal sedang dalam tahap penyusunan |
| `SUBMITTED` | Diajukan | info | Proposal telah diajukan, menunggu persetujuan Dekan |
| `NEED_ASSIGNMENT` | Perlu Persetujuan Anggota | warning | Anggota tim belum menerima undangan |
| `APPROVED` | Disetujui Dekan | primary | Menunggu persetujuan Kepala LPPM |
| `WAITING_REVIEWER` | Menunggu Penugasan Reviewer | cyan | Admin LPPM perlu menugaskan reviewer |
| `UNDER_REVIEW` | Sedang Direview | orange | Reviewer sedang melakukan review |
| `REVIEWED` | Review Selesai | purple | Semua reviewer selesai, menunggu keputusan |
| `REVISION_NEEDED` | Perlu Revisi | yellow | Proposal dikembalikan untuk perbaikan |
| `COMPLETED` | Selesai | success | Proposal disetujui (TERMINAL) |
| `REJECTED` | Ditolak | danger | Proposal ditolak (TERMINAL) |

### Status Review (`ReviewStatus`)

| Status | Label (ID) | Warna | Deskripsi |
|--------|------------|-------|-----------|
| `PENDING` | Menunggu Review | warning | Review belum dimulai |
| `IN_PROGRESS` | Sedang Direview | info | Reviewer membuka form review |
| `COMPLETED` | Review Selesai | success | Review telah disubmit |
| `RE_REVIEW_REQUESTED` | Perlu Review Ulang | orange | Proposal direvisi, perlu review ulang |

---

## Daftar Role

| Role | Deskripsi | Scope |
|------|-----------|-------|
| `superadmin` | IT / Developer | Full access |
| `admin lppm` | Staff operasional LPPM | Assign reviewer, monitoring |
| `kepala lppm` | Direktur LPPM | Initial & final approval |
| `dekan` | Dekan Fakultas | First-level approval (faculty-scoped) |
| `dosen` | Pengusul | Membuat & submit proposal |
| `reviewer` | Pakar evaluator | Review proposal yang ditugaskan |
| `rektor` | Pimpinan universitas | Strategic oversight (read-only) |

---

## Flow Diagram Utama

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                              FLOW UTAMA PROPOSAL                                    │
└─────────────────────────────────────────────────────────────────────────────────────┘

     DOSEN                DEKAN              KEPALA LPPM           ADMIN LPPM         REVIEWER
       │                    │                     │                    │                  │
       ▼                    │                     │                    │                  │
  ┌─────────┐               │                     │                    │                  │
  │  DRAFT  │               │                     │                    │                  │
  └────┬────┘               │                     │                    │                  │
       │ submit             │                     │                    │                  │
       ▼                    ▼                     │                    │                  │
  ┌───────────┐        ┌─────────┐                │                    │                  │
  │ SUBMITTED │───────►│ Review  │                │                    │                  │
  └───────────┘        └────┬────┘                │                    │                  │
                            │ approve             │                    │                  │
                            ▼                     ▼                    │                  │
                       ┌──────────┐          ┌─────────┐               │                  │
                       │ APPROVED │─────────►│ Review  │               │                  │
                       └──────────┘          └────┬────┘               │                  │
                                                  │ approve            │                  │
                                                  ▼                    ▼                  │
                                         ┌─────────────────┐      ┌─────────┐            │
                                         │WAITING_REVIEWER │─────►│ Assign  │            │
                                         └─────────────────┘      └────┬────┘            │
                                                                       │ assign          │
                                                                       ▼                 ▼
                                                                 ┌──────────────┐   ┌─────────┐
                                                                 │ UNDER_REVIEW │──►│ Review  │
                                                                 └──────────────┘   └────┬────┘
                                                                                         │ complete
                                                                       ┌─────────────────┘
                                                                       ▼
                                                                  ┌──────────┐
                                                                  │ REVIEWED │
                                                                  └────┬─────┘
                                                                       │
                                              ┌────────────────────────┼────────────────────────┐
                                              ▼                        ▼                        ▼
                                        ┌───────────┐          ┌─────────────────┐        ┌──────────┐
                                        │ COMPLETED │          │ REVISION_NEEDED │        │ REJECTED │
                                        │ (FINAL)   │          └────────┬────────┘        │ (FINAL)  │
                                        └───────────┘                   │                 └──────────┘
                                                                        │
                                                                        ▼
                                                              ┌─────────────────────┐
                                                              │ Kembali ke SUBMITTED│
                                                              │ (Siklus Revisi)     │
                                                              └─────────────────────┘
```

---

## Tahap 1: Penyusunan Proposal (Dosen)

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `dosen` |
| **Status Awal** | - (belum ada) |
| **Status Akhir** | `DRAFT` → `SUBMITTED` |
| **Tipe Proposal** | Research / Community Service |

### Halaman & Component

#### 1.1 Membuat Proposal Baru

| Aspek | Research | Community Service |
|-------|----------|-------------------|
| **URL** | `/research/proposal/create` | `/community-service/proposal/create` |
| **Route Name** | `research.proposal.create` | `community-service.proposal.create` |
| **Component** | `App\Livewire\Research\Proposal\Create` | `App\Livewire\CommunityService\Proposal\Create` |
| **Form Object** | `App\Livewire\Forms\ProposalForm` | `App\Livewire\Forms\ProposalForm` |
| **Abstract Class** | `App\Livewire\Abstracts\ProposalCreate` | `App\Livewire\Abstracts\ProposalCreate` |

#### Wizard Steps (Multi-Step Form)

```
Step 1: Identitas Usulan
├── Judul proposal
├── Skema penelitian (research_scheme_id)
├── Bidang fokus (focus_area_id)
├── Tema & Topik
├── Prioritas Nasional
└── Kluster Sains (Level 1, 2, 3)

Step 2: Identitas Pengusul
├── Ketua (otomatis: current user)
├── Anggota Tim (invite via email)
└── Tugas masing-masing anggota

Step 3: Substansi Usulan
├── Ringkasan/Abstrak
├── Latar Belakang
├── Tujuan
├── Metode
└── Tinjauan Pustaka

Step 4: Luaran & Target
├── Target luaran wajib
├── Target luaran tambahan
└── Indikator keberhasilan

Step 5: RAB (Rencana Anggaran Biaya)
├── Kategori belanja
├── Item belanja
├── Justifikasi
└── Total (max: SBK Value)

Step 6: Jadwal Kegiatan
├── Aktivitas per bulan
└── Milestone

Step 7: Dokumen Lampiran
├── CV Ketua & Anggota
├── Surat pernyataan
└── Dokumen pendukung lainnya
```

#### 1.2 Melihat & Edit Proposal

| Aspek | Research | Community Service |
|-------|----------|-------------------|
| **URL Show** | `/research/proposal/{proposal}` | `/community-service/proposal/{proposal}` |
| **URL Edit** | `/research/proposal/{proposal}/edit` | `/community-service/proposal/{proposal}/edit` |
| **Component Show** | `App\Livewire\Research\Proposal\Show` | `App\Livewire\CommunityService\Proposal\Show` |
| **Component Edit** | `App\Livewire\Research\Proposal\Edit` | `App\Livewire\CommunityService\Proposal\Edit` |
| **Abstract Class** | `App\Livewire\Abstracts\ProposalShow` | `App\Livewire\Abstracts\ProposalShow` |

#### 1.3 Manajemen Tim Anggota

| Aspek | Detail |
|-------|--------|
| **Component** | `App\Livewire\Research\Proposal\TeamMemberForm` |
| **Component Invitations** | `App\Livewire\Research\Proposal\TeamMemberInvitations` |
| **Tabel DB** | `proposal_user` (pivot) |
| **Status Anggota** | `pending`, `accepted`, `rejected` |

**Syarat Submit:**
```php
// Proposal.php
public function allTeamMembersAccepted(): bool
{
    $totalMembers = $this->teamMembers()->count();
    if ($totalMembers === 0) {
        return true;
    }
    $acceptedMembers = $this->teamMembers()
        ->wherePivot('status', 'accepted')
        ->count();
    return $totalMembers === $acceptedMembers;
}
```

#### 1.4 Submit Proposal

| Aspek | Detail |
|-------|--------|
| **Component** | `App\Livewire\Research\Proposal\SubmitButton` |
| **Action** | `App\Livewire\Actions\SubmitProposalAction` |
| **Validasi** | Semua anggota tim harus `accepted` |
| **Transisi** | `DRAFT` → `SUBMITTED` atau `NEED_ASSIGNMENT` → `SUBMITTED` atau `REVISION_NEEDED` → `SUBMITTED` |

**Logic SubmitProposalAction:**
```php
public function execute(Proposal $proposal): array
{
    // 1. Cek semua anggota sudah accept
    if (!$proposal->allTeamMembersAccepted()) {
        return ['success' => false, 'message' => '...'];
    }
    
    // 2. Cek status valid untuk submit
    $allowedStatuses = [
        ProposalStatus::DRAFT,
        ProposalStatus::NEED_ASSIGNMENT,
        ProposalStatus::REVISION_NEEDED,
    ];
    
    // 3. Update status ke SUBMITTED
    $proposal->update(['status' => ProposalStatus::SUBMITTED]);
    
    // 4. Kirim notifikasi ke Dekan
    $this->sendNotifications($proposal);
    
    // 5. Jika resubmission setelah revisi & ada reviewer, trigger re-review
    if ($isResubmissionAfterRevision && $proposal->reviewers()->exists()) {
        $this->triggerReReview($proposal);
    }
}
```

---

## Tahap 2: Validasi Dekan

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `dekan` |
| **Status Awal** | `SUBMITTED` |
| **Status Akhir** | `APPROVED` atau `NEED_ASSIGNMENT` |
| **Scope** | Faculty-scoped (hanya proposal dari fakultas sendiri) |

### Halaman & Component

| Aspek | Detail |
|-------|--------|
| **URL Daftar** | `/dekan/proposals` |
| **Route Name** | `dekan.proposals.index` |
| **Component Index** | `App\Livewire\Dekan\ProposalIndex` |
| **URL Riwayat** | `/dekan/riwayat-persetujuan` |
| **Component Riwayat** | `App\Livewire\Dekan\ApprovalHistory` |

### Proses Approval

| Aspek | Detail |
|-------|--------|
| **Component Button** | `App\Livewire\Research\Proposal\ApprovalButton` |
| **Trait** | `App\Livewire\Traits\WithApproval` |
| **Action** | `App\Livewire\Actions\DekanApprovalAction` |

**Logic DekanApprovalAction:**
```php
public function execute(Proposal $proposal, string $decision, ?string $notes = null): array
{
    // 1. Validasi status = SUBMITTED
    if ($proposal->status !== ProposalStatus::SUBMITTED) {
        return ['success' => false];
    }
    
    // 2. Validasi fakultas sama
    $dekanFacultyId = Auth::user()->identity?->faculty_id;
    $submitterFacultyId = $proposal->submitter->identity?->faculty_id;
    if ($dekanFacultyId !== $submitterFacultyId) {
        return ['success' => false];
    }
    
    // 3. Update status
    $newStatus = $decision === 'approved'
        ? ProposalStatus::APPROVED        // Lanjut ke Kepala LPPM
        : ProposalStatus::NEED_ASSIGNMENT; // Kembali untuk perbaikan tim
    
    $proposal->update(['status' => $newStatus]);
    
    // 4. Kirim notifikasi
    $this->sendNotifications($proposal, $decision);
}
```

### Keputusan Dekan

| Keputusan | Status Baru | Tindak Lanjut |
|-----------|-------------|---------------|
| **Setuju** | `APPROVED` | Diteruskan ke Kepala LPPM |
| **Perlu Perbaikan Tim** | `NEED_ASSIGNMENT` | Dikembalikan ke Dosen |

---

## Tahap 3: Persetujuan Awal Kepala LPPM

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `kepala lppm` |
| **Status Awal** | `APPROVED` |
| **Status Akhir** | `WAITING_REVIEWER` atau `UNDER_REVIEW` (jika resubmission) |

### Halaman & Component

| Aspek | Detail |
|-------|--------|
| **URL** | `/kepala-lppm/persetujuan-awal` |
| **Route Name** | `kepala-lppm.initial-approval` |
| **Component Index** | `App\Livewire\KepalaLppm\InitialApproval` |
| **Component Button** | `App\Livewire\Research\Proposal\KepalaLppmInitialApproval` |

### Proses Initial Approval

**Logic KepalaLppmInitialApproval:**
```php
public function approve(): void
{
    $proposal = $this->proposal;
    
    // Cek apakah sudah punya reviewer (resubmission)
    $hasExistingReviewers = $proposal->reviewers()->exists();
    
    if ($hasExistingReviewers) {
        // RESUBMISSION: Trigger re-review ke reviewer yang sama
        $reReviewAction = app(RequestReReviewAction::class);
        $reReviewAction->execute($proposal);
        
        // Langsung ke UNDER_REVIEW
        $proposal->update(['status' => ProposalStatus::UNDER_REVIEW]);
    } else {
        // FIRST SUBMISSION: Ke WAITING_REVIEWER
        $proposal->update(['status' => ProposalStatus::WAITING_REVIEWER]);
        
        // Notifikasi ke Admin LPPM untuk assign reviewer
        $this->notifyAdminLppm($proposal);
    }
}
```

### Perbedaan First Submission vs Resubmission

| Aspek | First Submission | Resubmission (Setelah Revisi) |
|-------|------------------|-------------------------------|
| **Status Akhir** | `WAITING_REVIEWER` | `UNDER_REVIEW` |
| **Reviewer** | Belum ada | Sudah ada dari sebelumnya |
| **Action Tambahan** | - | `RequestReReviewAction` |
| **Round** | 1 | Increment (2, 3, ...) |

---

## Tahap 4: Penugasan Reviewer (Admin LPPM)

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `admin lppm` |
| **Status Awal** | `WAITING_REVIEWER` |
| **Status Akhir** | `UNDER_REVIEW` |

### Halaman & Component

| Aspek | Detail |
|-------|--------|
| **URL Penugasan** | `/admin-lppm/penugasan-reviewer` |
| **Route Name** | `admin-lppm.assign-reviewers` |
| **Component Index** | `App\Livewire\AdminLppm\ReviewerAssignment` |
| **URL Monitoring** | `/admin-lppm/monitoring-review` |
| **Component Monitoring** | `App\Livewire\AdminLppm\ReviewMonitoring` |
| **URL Beban Kerja** | `/admin-lppm/beban-kerja-reviewer` |
| **Component Workload** | `App\Livewire\AdminLppm\ReviewerWorkload` |

### Proses Penugasan

| Aspek | Detail |
|-------|--------|
| **Component Form** | `App\Livewire\Research\Proposal\ReviewerAssignment` |
| **Tabel DB** | `proposal_reviewer` |
| **Default Deadline** | 14 hari dari assigned_at |

**Data yang dibuat di `proposal_reviewer`:**
```php
ProposalReviewer::create([
    'proposal_id' => $proposal->id,
    'user_id' => $reviewerId,
    'status' => ReviewStatus::PENDING,
    'round' => 1,
    'assigned_at' => now(),
    'deadline_at' => now()->addDays(14),
]);
```

### Transisi Status

Ketika reviewer pertama ditugaskan:
```
WAITING_REVIEWER → UNDER_REVIEW
```

---

## Tahap 5: Proses Review (Reviewer)

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `reviewer` |
| **Status Proposal** | `UNDER_REVIEW` |
| **Status Review** | `PENDING` → `IN_PROGRESS` → `COMPLETED` |

### Halaman & Component

| Aspek | Research | Community Service |
|-------|----------|-------------------|
| **URL Daftar** | `/review/research` | `/review/community-service` |
| **Route Name** | `review.research` | `review.community-service` |
| **Component** | `App\Livewire\Review\Research` | `App\Livewire\Review\CommunityService` |
| **URL Riwayat** | `/review/riwayat-review` | `/review/riwayat-review` |
| **Component Riwayat** | `App\Livewire\Review\ReviewHistory` | `App\Livewire\Review\ReviewHistory` |

### Form Review

| Aspek | Detail |
|-------|--------|
| **Component** | `App\Livewire\Research\Proposal\ReviewForm` |
| **Component Button** | `App\Livewire\Research\Proposal\ReviewerForm` |
| **Action** | `App\Livewire\Actions\CompleteReviewAction` |

### Alur Review

```
┌─────────────┐     Reviewer membuka form     ┌───────────────┐
│   PENDING   │ ─────────────────────────────►│  IN_PROGRESS  │
└─────────────┘     markAsStarted()           └───────┬───────┘
                                                      │
                                                      │ Submit review
                                                      ▼
                                              ┌───────────────┐
                                              │   COMPLETED   │
                                              └───────────────┘
```

### Isian Review

| Field | Tipe | Keterangan |
|-------|------|------------|
| `review_notes` | text | Catatan/masukan reviewer |
| `recommendation` | enum | `approved`, `rejected`, `revision_needed` |
| `completed_at` | datetime | Waktu selesai review |

### Logic CompleteReviewAction

```php
public function execute(ProposalReviewer $review, string $comments, string $recommendation): array
{
    // 1. Complete the review
    $review->complete($comments, $recommendation);
    
    // 2. Create review log for history
    $this->createReviewLog($review, $comments, $recommendation);
    
    // 3. Check if ALL reviewers completed
    $proposal = $review->proposal;
    if ($proposal->allReviewersCompleted()) {
        // Transition ke REVIEWED
        $proposal->update(['status' => ProposalStatus::REVIEWED]);
    }
}
```

### Transisi Status Proposal

```
Ketika SEMUA reviewer selesai:
UNDER_REVIEW → REVIEWED
```

---

## Tahap 6: Keputusan Final (Kepala LPPM)

### Overview

| Aspek | Detail |
|-------|--------|
| **Role** | `kepala lppm` |
| **Status Awal** | `REVIEWED` |
| **Status Akhir** | `COMPLETED` / `REJECTED` / `REVISION_NEEDED` |

### Halaman & Component

| Aspek | Detail |
|-------|--------|
| **URL** | `/kepala-lppm/persetujuan-akhir` |
| **Route Name** | `kepala-lppm.final-decision` |
| **Component Index** | `App\Livewire\KepalaLppm\FinalDecision` |
| **Component Button** | `App\Livewire\Research\Proposal\KepalaLppmFinalDecision` |

### Keputusan yang Tersedia

| Keputusan | Status Baru | Tindak Lanjut |
|-----------|-------------|---------------|
| **Setujui** | `COMPLETED` | Proposal selesai (TERMINAL) |
| **Tolak** | `REJECTED` | Proposal ditolak (TERMINAL) |
| **Perlu Revisi** | `REVISION_NEEDED` | Dikembalikan ke Dosen |

### Logic KepalaLppmFinalDecision

```php
public function processDecision(): void
{
    $newStatus = match ($this->decision) {
        'completed' => ProposalStatus::COMPLETED,
        'rejected' => ProposalStatus::REJECTED,
        'revision_needed' => ProposalStatus::REVISION_NEEDED,
    };
    
    // Validate transition
    if (!$proposal->status->canTransitionTo($newStatus)) {
        // Error
    }
    
    $proposal->update(['status' => $newStatus]);
    
    // Send notifications
    $this->sendNotifications($proposal, $this->decision);
}
```

---

## Tahap 7: Siklus Revisi (Loop)

### Overview

Ketika Kepala LPPM memutuskan `REVISION_NEEDED`, proposal masuk ke siklus revisi.

### Flow Siklus Revisi

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                               SIKLUS REVISI                                         │
└─────────────────────────────────────────────────────────────────────────────────────┘

  ┌─────────────────┐
  │ REVISION_NEEDED │  Kepala LPPM memutuskan perlu revisi
  └────────┬────────┘
           │
           │  Dosen memperbaiki proposal
           │  PAGE: /research/proposal/{id}/edit
           │
           ▼
  ┌─────────────────────────────────────────────────────────────────┐
  │  DOSEN SUBMIT ULANG                                             │
  │  ACTION: SubmitProposalAction                                   │
  │                                                                 │
  │  if ($isResubmissionAfterRevision && hasExistingReviewers) {   │
  │      RequestReReviewAction::execute()                           │
  │  }                                                              │
  └─────────────────────────────────────────────────────────────────┘
           │
           ▼
     ┌───────────┐
     │ SUBMITTED │  Kembali ke alur Dekan
     └─────┬─────┘
           │
           ▼
     [ DEKAN APPROVE ]
           │
           ▼
     ┌──────────┐
     │ APPROVED │
     └─────┬────┘
           │
           ▼
  ┌─────────────────────────────────────────────────────────────────┐
  │  KEPALA LPPM INITIAL APPROVAL                                   │
  │  Karena sudah ada reviewer, langsung ke UNDER_REVIEW            │
  │                                                                 │
  │  RequestReReviewAction triggered:                               │
  │  - round++ (1 → 2 → 3 ...)                                      │
  │  - reviewer status → RE_REVIEW_REQUESTED                        │
  │  - review_notes & recommendation dikosongkan                    │
  │  - deadline_at diset ulang (+14 hari)                           │
  │  - notifikasi ke reviewer                                       │
  └─────────────────────────────────────────────────────────────────┘
           │
           ▼
   ┌──────────────┐
   │ UNDER_REVIEW │  Reviewer melakukan review ulang (Siklus 2+)
   └──────────────┘
           │
           ▼
     [ PROSES REVIEW SAMA SEPERTI SIKLUS 1 ]
           │
           ▼
     ┌──────────┐
     │ REVIEWED │
     └────┬─────┘
          │
          ├──────────────┬──────────────┐
          ▼              ▼              ▼
    ┌───────────┐  ┌──────────┐  ┌─────────────────┐
    │ COMPLETED │  │ REJECTED │  │ REVISION_NEEDED │
    └───────────┘  └──────────┘  └────────┬────────┘
                                          │
                                          ▼
                                   [ ULANGI SIKLUS ]
```

### RequestReReviewAction

| Aspek | Detail |
|-------|--------|
| **Class** | `App\Livewire\Actions\RequestReReviewAction` |
| **Trigger** | Ketika proposal dengan reviewer existing di-approve Kepala LPPM |

**Logic:**
```php
public function execute(Proposal $proposal, int $daysToReview = 14): array
{
    // Get current max round
    $currentRound = $proposal->reviewers()->max('round') ?? 1;
    $newRound = $currentRound + 1;
    
    // Reset all reviewers for new round
    $proposal->reviewers()->update([
        'status' => ReviewStatus::RE_REVIEW_REQUESTED,
        'round' => $newRound,
        'review_notes' => null,
        'recommendation' => null,
        'started_at' => null,
        'completed_at' => null,
        'assigned_at' => now(),
        'deadline_at' => now()->addDays($daysToReview),
    ]);
    
    // Send notifications to all reviewers
    $this->sendNotifications($proposal, $newRound);
}
```

### Perubahan Data per Siklus

| Field | Siklus 1 | Siklus 2+ |
|-------|----------|-----------|
| `round` | 1 | 2, 3, 4, ... |
| `status` awal | `PENDING` | `RE_REVIEW_REQUESTED` |
| `review_notes` | Fresh | Dikosongkan |
| `recommendation` | Fresh | Dikosongkan |
| `deadline_at` | +14 hari | Diset ulang +14 hari |

### Tabel `review_logs` (Riwayat)

Setiap kali reviewer complete, dibuat record di `review_logs`:

| Field | Keterangan |
|-------|------------|
| `proposal_reviewer_id` | FK ke proposal_reviewer |
| `proposal_id` | FK ke proposal |
| `user_id` | FK ke user (reviewer) |
| `round` | Siklus ke berapa |
| `review_notes` | Catatan pada siklus tersebut |
| `recommendation` | Rekomendasi pada siklus tersebut |
| `completed_at` | Waktu selesai |

---

## Transisi Status Lengkap

```php
// ProposalStatus.php - canTransitionTo()

DRAFT           → [SUBMITTED]
SUBMITTED       → [APPROVED, NEED_ASSIGNMENT, REJECTED]
NEED_ASSIGNMENT → [SUBMITTED]
APPROVED        → [WAITING_REVIEWER, UNDER_REVIEW, REJECTED]
WAITING_REVIEWER→ [UNDER_REVIEW]
UNDER_REVIEW    → [REVIEWED]
REVIEWED        → [COMPLETED, REVISION_NEEDED, REJECTED]
REVISION_NEEDED → [SUBMITTED]
COMPLETED       → [] (TERMINAL)
REJECTED        → [] (TERMINAL)
```

### Diagram Transisi

```
                                    ┌─────────────────────────────────────────┐
                                    │                                         │
                                    ▼                                         │
┌───────┐     ┌───────────┐     ┌──────────┐     ┌─────────────────┐     ┌────┴────────┐
│ DRAFT │────►│ SUBMITTED │────►│ APPROVED │────►│WAITING_REVIEWER │────►│UNDER_REVIEW │
└───────┘     └─────┬─────┘     └────┬─────┘     └─────────────────┘     └──────┬──────┘
                    │                │                                          │
                    │                │                                          │
                    ▼                │                                          ▼
           ┌────────────────┐        │                                    ┌──────────┐
           │NEED_ASSIGNMENT │        │                                    │ REVIEWED │
           └───────┬────────┘        │                                    └────┬─────┘
                   │                 │                                         │
                   └─────────────────┼─────────────────────────────────────────┤
                                     │                                         │
                                     ▼                                         │
                               ┌──────────┐                                    │
                               │ REJECTED │◄───────────────────────────────────┤
                               └──────────┘                                    │
                                                                               │
                    ┌──────────────────────────────────────────────────────────┤
                    │                                                          │
                    ▼                                                          ▼
           ┌─────────────────┐                                          ┌───────────┐
           │ REVISION_NEEDED │                                          │ COMPLETED │
           └────────┬────────┘                                          └───────────┘
                    │
                    └──────────────────► [Kembali ke SUBMITTED]
```

---

## Mapping Page per Role

### Dosen

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/research` | `research.proposal.index` | Daftar proposal penelitian |
| `/research/proposal/create` | `research.proposal.create` | Buat proposal baru |
| `/research/proposal/{id}` | `research.proposal.show` | Detail proposal |
| `/research/proposal/{id}/edit` | `research.proposal.edit` | Edit proposal |
| `/research/proposal-revision` | `research.proposal-revision.index` | Daftar proposal perlu revisi |
| `/community-service` | `community-service.proposal.index` | Daftar proposal PKM |
| `/community-service/proposal/create` | `community-service.proposal.create` | Buat proposal PKM |
| `/research/progress-report` | `research.progress-report.index` | Laporan kemajuan |
| `/research/final-report` | `research.final-report.index` | Laporan akhir |
| `/research/daily-note` | `research.daily-note.index` | Catatan harian |

### Dekan

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/dekan/proposals` | `dekan.proposals.index` | Daftar proposal menunggu persetujuan |
| `/dekan/riwayat-persetujuan` | `dekan.approval-history` | Riwayat persetujuan |

### Kepala LPPM

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/kepala-lppm/persetujuan-awal` | `kepala-lppm.initial-approval` | Persetujuan awal (setelah Dekan) |
| `/kepala-lppm/persetujuan-akhir` | `kepala-lppm.final-decision` | Keputusan final (setelah review) |

### Admin LPPM

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/admin-lppm/penugasan-reviewer` | `admin-lppm.assign-reviewers` | Menugaskan reviewer |
| `/admin-lppm/monitoring-review` | `admin-lppm.review-monitoring` | Monitoring progress review |
| `/admin-lppm/beban-kerja-reviewer` | `admin-lppm.reviewer-workload` | Beban kerja reviewer |

### Reviewer

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/review/research` | `review.research` | Daftar proposal penelitian untuk direview |
| `/review/community-service` | `review.community-service` | Daftar proposal PKM untuk direview |
| `/review/riwayat-review` | `review.review-history` | Riwayat review yang sudah selesai |

### Semua Role

| URL | Nama Route | Fungsi |
|-----|------------|--------|
| `/dashboard` | `dashboard` | Dashboard utama (per role) |
| `/notifications` | `notifications` | Pusat notifikasi |
| `/settings` | `settings` | Pengaturan akun |
| `/laporan-penelitian` | `reports.research` | Laporan penelitian |
| `/laporan-pkm` | `reports.pkm` | Laporan PKM |
| `/laporan-luaran` | `reports.outputs` | Laporan luaran |

---

## Catatan Teknis

### File Lokasi Utama

| Kategori | Path |
|----------|------|
| **Enums** | `app/Enums/ProposalStatus.php`, `app/Enums/ReviewStatus.php` |
| **Models** | `app/Models/Proposal.php`, `app/Models/ProposalReviewer.php`, `app/Models/ReviewLog.php` |
| **Actions** | `app/Livewire/Actions/*.php` |
| **Abstract Classes** | `app/Livewire/Abstracts/*.php` |
| **Traits** | `app/Livewire/Traits/*.php` |
| **Forms** | `app/Livewire/Forms/*.php` |
| **Services** | `app/Services/ProposalService.php`, `app/Services/NotificationService.php` |

### Database Tables

| Tabel | Fungsi |
|-------|--------|
| `proposals` | Data utama proposal |
| `proposal_user` | Pivot tabel tim anggota |
| `proposal_reviewer` | Penugasan reviewer |
| `review_logs` | Riwayat review per siklus |
| `proposal_status_logs` | Log perubahan status |

---

*Dokumentasi ini dihasilkan berdasarkan analisis codebase pada Januari 2026.*
