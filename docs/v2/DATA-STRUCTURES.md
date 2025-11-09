# Data Structures v2.0
## SIM LPPM ITSNU - Research vs PKM Proposal Differences

**Document Version:** 2.0  
**Last Updated:** 2025-11-09  
**Polymorphic Relationship:** `proposals.detailable` → `research` OR `community_services`

---

## Polymorphic Relationship Overview

### Proposal Table Structure

```php
proposals {
    detailable_type: 'App\Models\Research' | 'App\Models\CommunityService'
    detailable_id: UUID (foreign key to research.id or community_services.id)
}
```

### Relationship Implementation

```php
// Proposal model
public function detailable(): MorphTo
{
    return $this->morphTo();
}

// Research model
public function proposal(): MorphOne
{
    return $this->morphOne(Proposal::class, 'detailable');
}

// CommunityService model
public function proposal(): MorphOne
{
    return $this->morphOne(Proposal::class, 'detailable');
}
```

---

## Research Proposal Data

### Table: `research`

**Required Fields:**

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | uuid | ✅ | Primary key |
| macro_research_group_id | bigint | ✅ | Research category FK |
| final_tkt_target | integer | ✅ | TKT level (0-9) |
| background | longText | ✅ | Research background |
| state_of_the_art | longText | ✅ | Literature review |
| methodology | longText | ✅ | Research methods |
| roadmap_data | json | ✅ | Multi-year roadmap |
| substance_file | string | ❌ | Uploaded document |

### TKT (Technology Readiness Level)

**Scale:** 0-9

| Level | Description |
|-------|-------------|
| 0-2 | Basic Research (concept, principles) |
| 3-4 | Applied Research (proof-of-concept, prototype) |
| 5-6 | Development (pilot scale, demonstration) |
| 7-9 | Deployment (production, commercialization) |

**Usage in Research:**
- `final_tkt_target`: Target TKT level at project completion
- Tracked in `roadmap_data` JSON for each year
- Measured in progress reports

---

### Roadmap Data (JSON Structure)

```json
{
  "year_1": {
    "activities": [
      "Studi literatur mendalam",
      "Desain eksperimen awal",
      "Pengumpulan data primer"
    ],
    "targets": [
      "Publikasi artikel jurnal (Q3)",
      "Prototipe tahap 1 selesai"
    ],
    "tkt_level": 3,
    "budget_allocation": 30000000
  },
  "year_2": {
    "activities": [
      "Validasi prototipe",
      "Uji coba skala laboratorium",
      "Analisis hasil eksperimen"
    ],
    "targets": [
      "Publikasi jurnal internasional (Q2)",
      "Pengajuan paten"
    ],
    "tkt_level": 5,
    "budget_allocation": 40000000
  },
  "year_3": {
    "activities": [
      "Uji coba skala pilot",
      "Sosialisasi hasil penelitian",
      "Penyusunan laporan akhir"
    ],
    "targets": [
      "Prototipe final",
      "Publikasi prosiding internasional",
      "Buku monograf"
    ],
    "tkt_level": 7,
    "budget_allocation": 30000000
  }
}
```

**Validation:**
- Must have at least `year_1`
- Each year must have `activities`, `targets`, `tkt_level`
- `tkt_level` must progress (year_2 >= year_1)

---

### State of the Art (Literature Review)

**Required Content:**
- Recent research (last 5 years)
- Key findings and gaps
- Theoretical framework
- Methodology references
- Research novelty justification

**Length:** Typically 500-1000 words

---

### Methodology (Research Methods)

**Required Content:**
- Research design (qualitative, quantitative, mixed)
- Data collection methods
- Sampling strategy
- Analysis techniques
- Tools and instruments
- Validity and reliability measures

**Length:** Typically 300-700 words

---

### Macro Research Group

**Table:** `macro_research_groups`

**Examples:**
- Ilmu Komputer dan Informatika
- Teknik Elektro dan Elektronika
- Bioteknologi dan Kesehatan
- Energi Terbarukan
- Material dan Nanoteknologi

**Purpose:** Categorize research by broad domain for institutional tracking

---

### Research-Specific Outputs

**Typical Outputs:**
- Journal articles (Q1, Q2, Q3, Q4)
- Conference proceedings (international, national)
- Patents (filed, granted)
- Prototypes (TKT-based)
- Books/monographs
- Technology transfer

---

## PKM (Community Service) Proposal Data

### Table: `community_services`

**Required Fields:**

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | uuid | ✅ | Primary key |
| partner_id | bigint | ❌ | Main partner FK (optional) |
| partner_issue_summary | text | ✅ | Community problem |
| solution_offered | text | ✅ | Proposed solution |

---

### Partner Integration

**Main Partner (optional):**
- `community_services.partner_id` → `partners.id`
- One primary partner per PKM proposal

**Multiple Partners:**
- `proposal_partner` pivot table (M:N)
- PKM proposals can collaborate with multiple partners

**Partner Types:**
- NGO (Non-Governmental Organization)
- Community (Kelompok Masyarakat)
- Government (Pemerintah/Dinas)
- School (Sekolah/Pendidikan)
- SME (Small-Medium Enterprise)

---

### Partner Issue Summary

**Required Content:**
- Community problem description
- Impact and urgency
- Stakeholders affected
- Current situation analysis
- Why this problem matters

**Length:** Typically 300-500 words

**Example:**
```
Desa XYZ mengalami masalah rendahnya literasi digital di kalangan 
UMKM lokal. 85% UMKM belum memanfaatkan platform digital untuk 
pemasaran, menyebabkan penjualan stagnan di era pandemi. Pengusaha 
lokal kesulitan mengakses pasar yang lebih luas dan bersaing dengan 
produk dari luar daerah. Diperlukan pelatihan dan pendampingan 
intensif untuk meningkatkan kapasitas digital UMKM.
```

---

### Solution Offered

**Required Content:**
- Proposed intervention approach
- Activities and methods
- Expected outcomes
- Community engagement strategy
- Sustainability plan

**Length:** Typically 400-600 words

**Example:**
```
Program pelatihan dan pendampingan literasi digital UMKM melalui 
4 tahap: (1) Pelatihan dasar platform digital (marketplace, sosial media), 
(2) Workshop fotografi produk dan copywriting, (3) Pendampingan 
pembuatan akun bisnis, (4) Monitoring dan evaluasi selama 3 bulan. 
Melibatkan 30 UMKM sebagai mitra langsung dengan target 80% UMKM 
memiliki akun bisnis aktif dan peningkatan penjualan minimal 30%.
```

---

### PKM-Specific Outputs

**Typical Outputs:**
- Community empowerment programs
- Training workshops delivered
- Publications (media, articles, videos)
- Product prototypes for community
- Service delivery (consultations, assistance)
- Community impact reports
- Partner satisfaction certificates

---

## Comparison Table: Research vs PKM

| Aspect | Research (Penelitian) | PKM (Pengabdian Masyarakat) |
|--------|----------------------|----------------------------|
| **Primary Goal** | Scientific discovery, knowledge creation | Community problem-solving, social impact |
| **Output Focus** | Publications, patents, prototypes | Community empowerment, services |
| **Partners** | Optional (academic collaborators) | Required (community organizations) |
| **Methodology** | Required (scientific methods, rigorous design) | Not required (action-oriented, flexible) |
| **TKT Tracking** | Required (0-9 scale, progression) | Not applicable |
| **Roadmap** | Required (multi-year JSON structure) | Not required (activity schedule sufficient) |
| **State of Art** | Required (literature review, novelty) | Not required (problem identification) |
| **Background** | Required (theoretical framework, gap analysis) | Replaced by partner issue summary |
| **Solution** | Implicit in methodology | Explicit (solution_offered field) |
| **Macro Research Group** | Required | Not applicable |
| **Partner Issue** | Not applicable | Required (community problem) |
| **Evaluation Criteria** | Scientific rigor, novelty, feasibility | Social impact, community benefit, sustainability |
| **Typical Duration** | 1-3 years | 1-2 years (shorter cycles) |
| **Budget Focus** | Equipment, lab materials, conferences | Training materials, community activities, travel |

---

## Common Proposal Data (Shared)

Both Research and PKM proposals use the same structure for:

### 1. Team Members (`proposal_user`)

```php
{
    "proposal_id": "uuid",
    "user_id": "uuid",
    "role": "ketua|anggota",
    "tasks": "Specific responsibilities text",
    "status": "pending|accepted|rejected"
}
```

---

### 2. Budget Items (`budget_items`)

```php
{
    "proposal_id": "uuid",
    "budget_group_id": "bigint",      // e.g., "Honor", "Equipment"
    "budget_component_id": "bigint",  // e.g., "Honor.Speaker"
    "item_description": "text",
    "volume": "integer",
    "unit_price": "decimal(15,2)",
    "total_price": "decimal(15,2)"    // volume × unit_price (auto)
}
```

**Budget Differences:**
- **Research:** More equipment, lab materials, conference travel
- **PKM:** More training materials, community activities, local travel

---

### 3. Activity Schedules (`activity_schedules`)

```php
{
    "proposal_id": "uuid",
    "activity_name": "string",
    "year": "integer",
    "start_month": "integer (1-12)",
    "end_month": "integer (1-12)"
}
```

**Granularity:**
- **Research:** Often yearly breakdown with major milestones
- **PKM:** More frequent activities (monthly workshops, weekly visits)

---

### 4. Proposal Outputs (`proposal_outputs`)

```php
{
    "proposal_id": "uuid",
    "output_year": "integer",
    "category": "required|additional",
    "type": "string",                  // e.g., "journal", "workshop"
    "target_status": "string",         // e.g., "Q1", "Completed"
    "output_description": "text",
    "journal_name": "string",          // for journals
    "estimated_date": "date"
}
```

**Output Types:**
- **Research:** journal, patent, prototype, book, conference
- **PKM:** workshop, training, consultation, video, article, certificate

---

### 5. Research Stages (`research_stages`)

**Used by Both (despite name):**

```php
{
    "proposal_id": "uuid",
    "stage_number": "integer",
    "process_name": "string",
    "outputs": "text",
    "indicator": "string",
    "person_in_charge_id": "uuid"
}
```

**Usage:**
- **Research:** Phased research development (design → experiment → analysis)
- **PKM:** Service delivery stages (preparation → implementation → evaluation)

---

### 6. Keywords (`proposal_keyword`)

```php
{
    "proposal_id": "uuid",
    "keyword_id": "bigint"
}
```

**Keyword Examples:**
- **Research:** "machine learning", "nanotechnology", "clinical trial"
- **PKM:** "community empowerment", "SME development", "digital literacy"

---

### 7. Partners (`proposal_partner`) - Both Can Use

```php
{
    "proposal_id": "uuid",
    "partner_id": "bigint"
}
```

**Usage:**
- **Research:** Optional academic/industry collaborators
- **PKM:** Required community organizations

---

## Determining Proposal Type

### In Code

```php
$proposal = Proposal::find($id);

if ($proposal->detailable instanceof Research) {
    // Research proposal
    $tkt = $proposal->detailable->final_tkt_target;
    $methodology = $proposal->detailable->methodology;
    $roadmap = $proposal->detailable->roadmap_data;
} 
elseif ($proposal->detailable instanceof CommunityService) {
    // PKM proposal
    $issue = $proposal->detailable->partner_issue_summary;
    $solution = $proposal->detailable->solution_offered;
    $partner = $proposal->detailable->partner;
}
```

### In Livewire Components

```php
// Research proposal create route
Route::get('/research/proposal/create', Research\Proposal\Create::class);

// PKM proposal create route
Route::get('/community-service/proposal/create', CommunityService\Proposal\Create::class);
```

**Separate Components:**
- `app/Livewire/Research/Proposal/` - Research-specific
- `app/Livewire/CommunityService/Proposal/` - PKM-specific
- Shared logic in `app/Livewire/Forms/ProposalForm.php`

---

## Validation Differences

### Research Validation

```php
// Research-specific required fields
'macro_research_group_id' => 'required|exists:macro_research_groups,id',
'final_tkt_target' => 'required|integer|min:0|max:9',
'background' => 'required|string|min:100',
'state_of_the_art' => 'required|string|min:100',
'methodology' => 'required|string|min:100',
'roadmap_data' => 'required|json',
```

### PKM Validation

```php
// PKM-specific required fields
'partner_issue_summary' => 'required|string|min:100',
'solution_offered' => 'required|string|min:100',
'partner_id' => 'nullable|exists:partners,id',
```

---

## Progress Reporting Differences

Both use the same `progress_reports` table structure, but:

### Research Progress Reports

**Focus:**
- TKT progression
- Experiment results
- Data analysis updates
- Publication status
- Prototype development

**Mandatory Outputs:**
- Journal draft/submission
- Patent filing
- Prototype milestones
- Conference presentations

---

### PKM Progress Reports

**Focus:**
- Activities conducted (workshops, training)
- Community participation rates
- Social impact indicators
- Partner feedback
- Sustainability measures

**Mandatory Outputs:**
- Workshop completion certificates
- Training attendance reports
- Community testimonials
- Documentation (photos, videos)
- Partner satisfaction surveys

---

## UI Differences

### Research Proposal Form

**Sections:**
1. Basic Information (title, summary, taxonomy)
2. Research Background & Literature Review
3. Methodology & Approach
4. Roadmap & TKT Targets
5. Team & Responsibilities
6. Budget Plan
7. Expected Outputs (publications, patents)
8. Activity Schedule

**Color Theme:** Blue/Navy (academic)

---

### PKM Proposal Form

**Sections:**
1. Basic Information (title, summary, taxonomy)
2. Partner & Community Context
3. Problem Identification (issue summary)
4. Solution Design (solution offered)
5. Team & Responsibilities
6. Budget Plan
7. Expected Outputs (training, services)
8. Activity Schedule

**Color Theme:** Green/Teal (community)

---

## Database Query Examples

### Get All Research Proposals

```php
$researchProposals = Proposal::where('detailable_type', 'App\Models\Research')
    ->with('detailable.macroResearchGroup')
    ->get();
```

### Get All PKM Proposals

```php
$pkmProposals = Proposal::where('detailable_type', 'App\Models\CommunityService')
    ->with('detailable.partner')
    ->get();
```

### Get Proposals by Type for Specific User

```php
$userResearch = auth()->user()
    ->submittedProposals()
    ->whereHasMorph('detailable', Research::class)
    ->get();

$userPKM = auth()->user()
    ->submittedProposals()
    ->whereHasMorph('detailable', CommunityService::class)
    ->get();
```

---

**Document End**
