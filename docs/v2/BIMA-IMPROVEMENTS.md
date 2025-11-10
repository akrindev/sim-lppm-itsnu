# BIMA Kemdikbud - Recommended Improvements for SIM LPPM ITSNU

**Document Version:** 1.0  
**Date:** 2025-11-09  
**Purpose:** Comprehensive improvement suggestions based on BIMA Kemdikbud analysis

---

## Executive Summary

After analyzing **BIMA Kemdikbud's structure** for Penelitian vs Pengabdian kepada Masyarakat and reviewing the current SIM LPPM ITSNU implementation, here are critical improvements needed to ensure full alignment with national standards.

---

## 1. Database Schema Improvements

### 1.1 Add Missing Fields to Existing Tables

#### `research_schemes` Table
```sql
ALTER TABLE research_schemes ADD COLUMN description TEXT AFTER strata;
ALTER TABLE research_schemes MODIFY COLUMN strata 
    ENUM('Dasar', 'Terapan', 'Pengembangan', 'PKM') 
    COMMENT 'Strata Penelitian/PKM';
```

**Rationale:** Seeders now include descriptions, and 'PKM' strata is needed for Pemberdayaan Wilayah (PW) and Pemberdayaan Desa Binaan (PDB).

#### `national_priorities` Table
```sql
ALTER TABLE national_priorities ADD COLUMN description TEXT AFTER name;
```

**Rationale:** Each PRN has detailed descriptions from Perpres No. 38/2018 that should be stored.

#### `budget_groups` Table (Already has description - ✅ Good)
No changes needed, but update codes:
- Change 'HON' → 'HONOR'
- Change 'PER' → 'TEKNOLOGI'
- Change 'BHP' → (merge into TEKNOLOGI)
- Change 'PRJ' → 'PERJALANAN'
- Change 'LAN' → 'LAINNYA'
- Add new: 'PELATIHAN'

---

## 2. Penelitian vs Pengabdian: Critical Differences from BIMA

### 2.1 Form Fields Comparison

| Field | Penelitian (Research) | Pengabdian (PKM) | Current Implementation |
|-------|----------------------|------------------|------------------------|
| **TKT (Technology Readiness Level)** | ✅ Required (BIMA has auto-calc tool) | ❌ Not applicable | ⚠️ **ISSUE:** research table has TKT fields, but no PKM-specific fields |
| **Roadmap** | ✅ Research roadmap (technical milestones) | ✅ Social impact roadmap | ✅ Both have roadmap JSON field |
| **Methodology** | ✅ Detailed research methodology | ⚠️ Different (implementation approach) | ✅ research.methodology vs community_service.solution |
| **Partner/Mitra** | Optional (collaboration) | ✅ Required (MOU mandatory) | ✅ Both have partner tables |
| **Target Luaran** | Publications, Patents, Products | Social impact, Training outputs | ❌ **MISSING**: No dedicated output taxonomy |
| **Budget Validation** | By TKT and scheme | By community impact | ⚠️ **NEEDS:** Percentage validationper BIMA limits |

---

### 2.2 TKT (Tingkat Kesiapan Teknologi) Handling

**BIMA Implementation:**
- **Auto-calculate TKT** from indicators (tool "Ukur")
- **TKT scale:** 1-9 (from basic research to commercialization)
- **Mandatory fields:** Current TKT + Target TKT
- **Validation:** Target TKT must be >= Current TKT

**SIM LPPM ITSNU Current State:**
```php
// researches table
'tkt_level' => 'nullable|integer|min:1|max:9'
'target_tkt_level' => 'nullable|integer|min:1|max:9'
```

**✅ Status:** Already implemented!

**Recommendation:** Add TKT calculator UI (JavaScript tool) that matches BIMA's auto-calculation logic.

---

### 2.3 Roadmap Structure Differences

**PENELITIAN Roadmap (Technical):**
```json
{
  "year_1": {
    "activities": ["Research design", "Literature review", "Data collection"],
    "outputs": ["Conference paper", "Progress report"],
    "tkt_target": 3
  },
  "year_2": {
    "activities": ["Prototype development", "Testing"],
    "outputs": ["Journal publication", "Patent application"],
    "tkt_target": 5
  }
}
```

**PENGABDIAN Roadmap (Social Impact):**
```json
{
  "phase_1": {
    "activities": ["Community assessment", "Needs analysis", "Training design"],
    "outputs": ["Training modules", "Participant materials"],
    "partner_involvement": ["Local government", "Community leaders"]
  },
  "phase_2": {
    "activities": ["Implementation", "Monitoring"],
    "outputs": ["Trained participants", "Adoption reports"],
    "sustainability_plan": "Community continues activities independently"
  }
}
```

**Current Implementation:** Both use `roadmap_json` (TEXT column) ✅ Good flexibility

**Recommendation:** Create roadmap templates/validation schemas per proposal type.

---

## 3. Master Data: Luaran/Output Taxonomy

### 3.1 Missing Output Type Classification

**BIMA Structure for Penelitian:**

| Category | Group | Type | Target Status | Examples |
|----------|-------|------|---------------|----------|
| **Publikasi** | Jurnal | Nasional Terakreditasi | Accepted | Jurnal Sinta 1-6 |
| **Publikasi** | Jurnal | Internasional Bereputasi | Published | Scopus Q1-Q4 |
| **Publikasi** | Prosiding | Internasional | Presented | IEEE Conference |
| **HKI** | Paten | Paten Sederhana | Granted | Sertifikat Paten |
| **HKI** | Paten | Paten Penuh | Filed | Permohonan Paten |
| **HKI** | Hak Cipta | Software | Registered | Hak Cipta Aplikasi |
| **Produk** | Teknologi | Prototype | TKT 5 | Prototipe Alat |
| **Produk** | Teknologi | Produk Komersial | Commercialized | Produk Siap Pasar |

**BIMA Structure for Pengabdian:**

| Category | Group | Type | Target Status | Examples |
|----------|-------|------|---------------|----------|
| **Pemberdayaan** | Pelatihan | Capacity Building | Certified | Sertifikat Pelatihan |
| **Pemberdayaan** | Adopsi | Technology Adoption | Implemented | Teknologi Diterapkan |
| **Pemberdayaan** | Ekonomi | Income Improvement | Measurable | Peningkatan Pendapatan |
| **Publikasi** | Jurnal | Jurnal Pengabdian | Published | J-ABDI, etc. |
| **Produk** | Modul | Training Modules | Distributed | Modul Pelatihan |
| **Produk** | Video | Educational Media | Published | Video Edukasi |

**Current Implementation:**
```php
// proposal_outputs table
'category' => 'string'  // Free text - ❌ Not standardized
'group' => 'string'     // Free text - ❌ Not standardized
'type' => 'string'      // Free text - ❌ Not standardized
'target_status' => 'string'
```

**⚠️ CRITICAL ISSUE:** No master data table for output types!

**Recommendation:** Create `output_types` master table with BIMA taxonomy.

---

### 3.2 Budget Component Updates

**BIMA 2024-2025 RAB Structure with Percentages:**

| Component | Min % | Max % | Applies To |
|-----------|-------|-------|------------|
| **Upah dan Jasa (Honor)** | - | 10% | All |
| **Teknologi & Inovasi** | 50% | - | Research (Bahan + Peralatan combined) |
| **Pelatihan** | - | 20% | Pengabdian (primary), Research (optional) |
| **Perjalanan** | - | 15% | All |
| **Lainnya** | - | 5% | All |

**Current Implementation Issues:**
1. ❌ Separate BHP (Bahan Habis Pakai) and PER (Peralatan) - Should be merged as "Teknologi & Inovasi"
2. ❌ No "Pelatihan" component
3. ❌ No percentage validation in database or forms

**Recommendation:**
1. Update `BudgetGroupSeeder` (✅ Already done in this update!)
2. Add validation in `BudgetItem` model:
```php
public static function validateBudgetPercentages($proposalId)
{
    $budgetGroups = BudgetItem::where('proposal_id', $proposalId)
        ->join('budget_components', 'budget_items.budget_component_id', '=', 'budget_components.id')
        ->join('budget_groups', 'budget_components.budget_group_id', '=', 'budget_groups.id')
        ->selectRaw('budget_groups.code, SUM(budget_items.quantity * budget_items.unit_price) as total')
        ->groupBy('budget_groups.code')
        ->pluck('total', 'code');
    
    $totalBudget = $budgetGroups->sum();
    
    // Validate percentages
    $errors = [];
    if (($budgetGroups['HONOR'] / $totalBudget) > 0.10) {
        $errors[] = 'Honor exceeds 10% limit';
    }
    if (($budgetGroups['TEKNOLOGI'] / $totalBudget) < 0.50) {
        $errors[] = 'Teknologi & Inovasi must be at least 50%';
    }
    // ... more validations
    
    return $errors;
}
```

---

## 4. Eligibility Checking (SINTA & PDDIKTI Integration)

### 4.1 BIMA's Eligibility System

**BIMA validates:**
1. **SINTA Score** → Determines eligible schemes
2. **Klaster Institusi** → PT Akademik vs Vokasi
3. **Homebase Dosen** → Must match with institution
4. **Jabatan Fungsional** → Lektor, Lektor Kepala, etc.
5. **Status Aktif Mengajar** → From PDDIKTI
6. **Track Record** → Proposal yang sedang berjalan + laporan tanggungan

**Current Implementation:** ❌ No automatic eligibility checking

**Recommendation - Phase 1 (Manual):**
Add eligibility fields to `users` table:
```sql
ALTER TABLE users ADD COLUMN sinta_id VARCHAR(50) AFTER nidn;
ALTER TABLE users ADD COLUMN sinta_score INT AFTER sinta_id;
ALTER TABLE users ADD COLUMN klaster VARCHAR(50) AFTER sinta_score;
ALTER TABLE users ADD COLUMN jabatan_fungsional VARCHAR(100) AFTER homebase;
ALTER TABLE users ADD COLUMN status_aktif BOOLEAN DEFAULT true;
```

**Recommendation - Phase 2 (Future):**
Integrate with SINTA API and PDDIKTI API (if available) for auto-sync.

---

## 5. Proposal Workflow Enhancements

### 5.1 BIMA's 5-Stage Submission

**BIMA Stages:**
1. **Identitas Usulan** (Proposal Identity)
2. **Substansi Usulan** (Proposal Substance)
3. **RAB** (Budget Plan)
4. **Dokumen Pendukung** (Supporting Documents)
5. **Konfirmasi Usulan** (Proposal Confirmation)

**Current Implementation:** Single-form submission - works, but less guided

**Recommendation:** Keep current approach (works well), but add progress indicators:
```blade
<div class="proposal-progress">
    <div class="step active">1. Identitas</div>
    <div class="step">2. Substansi</div>
    <div class="step">3. Anggaran</div>
    <div class="step">4. Dokumen</div>
    <div class="step">5. Konfirmasi</div>
</div>
```

---

### 5.2 Draft vs Submitted Status

**BIMA Logic:**
- **Draft:** Can be edited freely
- **Submitted:** Locked, waiting for LPPM approval
- Once submitted, cannot revert to Draft without admin action

**Current Implementation:** Uses `ProposalStatus` enum, but no explicit "Draft" state

**Recommendation:** Add DRAFT to `ProposalStatus` enum:
```php
enum ProposalStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';  // Equivalent to BIMA's "Submitted"
    // ... rest
}
```

---

## 6. Partnership/Mitra Management

### 6.1 MOU Requirements for Pengabdian

**BIMA Requirement:** Pengabdian proposals MUST include signed MOU with community partners.

**Current Implementation:**
```php
// partners table exists ✅
// But: No MOU document upload field specifically for PKM
```

**Recommendation:** Add MOU tracking:
```sql
ALTER TABLE partners ADD COLUMN mou_document_path VARCHAR(255) AFTER type;
ALTER TABLE partners ADD COLUMN mou_signed_date DATE AFTER mou_document_path;
ALTER TABLE partners ADD COLUMN mou_status ENUM('draft', 'signed', 'expired') DEFAULT 'draft';
```

---

## 7. Reporting: Luaran vs Laporan

### 7.1 Distinguish Outputs (Luaran) from Progress Reports

**BIMA Structure:**
- **Luaran (Outputs):** Publications, patents, products → Tracked in `proposal_outputs`
- **Laporan Kemajuan (Progress Reports):** Quarterly/annual narrative reports → Different table
- **Laporan Akhir (Final Report):** End-of-grant comprehensive report

**Current Implementation:** `proposal_outputs` table exists ✅

**Recommendation:** Add `proposal_reports` table for narrative reports:
```sql
CREATE TABLE proposal_reports (
    id BIGINT UNSIGNED PRIMARY KEY,
    proposal_id BIGINT UNSIGNED NOT NULL,
    report_type ENUM('kemajuan', 'akhir') NOT NULL,
    reporting_period VARCHAR(50),  -- 'Q1 2025', 'Annual 2025'
    submitted_at TIMESTAMP,
    approved_at TIMESTAMP,
    status ENUM('draft', 'submitted', 'revision_needed', 'approved'),
    document_path VARCHAR(255),
    reviewer_notes TEXT,
    FOREIGN KEY (proposal_id) REFERENCES proposals(id) ON DELETE CASCADE
);
```

---

## 8. UI/UX Enhancements

### 8.1 Proposal Type Selection

**Recommendation:** At proposal creation, clearly distinguish:

```blade
<div class="proposal-type-selector">
    <div class="card penelitian">
        <h3>Penelitian</h3>
        <p>Research with TKT tracking, publications, patents</p>
        <button wire:click="selectType('penelitian')">Create Research Proposal</button>
    </div>
    
    <div class="card pengabdian">
        <h3>Pengabdian kepada Masyarakat (PKM)</h3>
        <p>Community service with social impact and partnership</p>
        <button wire:click="selectType('pengabdian')">Create PKM Proposal</button>
    </div>
</div>
```

---

### 8.2 Budget Calculator with Real-Time Percentage Display

**Recommendation:** Show live percentage breakdown as user enters budget items:

```blade
<div class="budget-summary">
    <div class="budget-group">
        <span>Honor:</span>
        <span class="percentage {{ $honorPercent > 10 ? 'text-danger' : '' }}">
            {{ number_format($honorPercent, 1) }}% (Max 10%)
        </span>
    </div>
    <div class="budget-group">
        <span>Teknologi & Inovasi:</span>
        <span class="percentage {{ $teknologiPercent < 50 ? 'text-warning' : '' }}">
            {{ number_format($teknologiPercent, 1) }}% (Min 50%)
        </span>
    </div>
    <!-- ... more -->
</div>
```

---

## 9. Migration Plan

### Phase 1: Critical (Immediate - Week 1-2)
1. ✅ Update seeders (DONE)
2. Create migrations for description fields
3. Add 'PKM' to research_schemes strata enum
4. Update BudgetComponentSeeder to match new groups
5. Test seed all data

### Phase 2: High Priority (Week 3-4)
1. Create `output_types` master table with BIMA taxonomy
2. Add budget percentage validation logic
3. Add DRAFT status to ProposalStatus enum
4. Update forms to show BIMA-aligned fields

### Phase 3: Medium Priority (Month 2)
1. Add eligibility fields to users table
2. Create `proposal_reports` table
3. Add MOU fields to partners table
4. Implement TKT calculator UI

### Phase 4: Future Enhancements (Month 3+)
1. SINTA/PDDIKTI API integration
2. Multi-step proposal creation wizard
3. Advanced budget calculator with live validation
4. Output tracking dashboard

---

## 10. Code Implementation Examples

### 10.1 Create Migration for Description Fields

```bash
php artisan make:migration add_description_to_research_schemes_and_priorities
```

```php
// Migration content
public function up(): void
{
    Schema::table('research_schemes', function (Blueprint $table) {
        $table->text('description')->nullable()->after('strata');
        $table->dropColumn('strata');
    });
    
    Schema::table('research_schemes', function (Blueprint $table) {
        $table->enum('strata', ['Dasar', 'Terapan', 'Pengembangan', 'PKM'])
              ->after('name')
              ->comment('Strata Penelitian/PKM');
    });
    
    Schema::table('national_priorities', function (Blueprint $table) {
        $table->text('description')->nullable()->after('name');
    });
}
```

### 10.2 Create OutputType Model and Migration

```bash
php artisan make:model OutputType -m
```

```php
// Migration
public function up(): void
{
    Schema::create('output_types', function (Blueprint $table) {
        $table->id();
        $table->enum('proposal_type', ['penelitian', 'pengabdian'])->comment('Applies to which type');
        $table->string('category')->comment('Publikasi, HKI, Produk, Pemberdayaan');
        $table->string('group')->comment('Jurnal, Paten, Teknologi, Pelatihan');
        $table->string('type')->comment('Specific type');
        $table->string('target_status')->comment('Expected status');
        $table->text('description')->nullable();
        $table->timestamps();
        
        $table->index(['proposal_type', 'category']);
    });
}

// Seeder
$outputTypes = [
    // PENELITIAN
    ['proposal_type' => 'penelitian', 'category' => 'Publikasi', 'group' => 'Jurnal', 'type' => 'Nasional Terakreditasi', 'target_status' => 'Published', 'description' => 'Jurnal Sinta 1-6'],
    ['proposal_type' => 'penelitian', 'category' => 'Publikasi', 'group' => 'Jurnal', 'type' => 'Internasional Bereputasi', 'target_status' => 'Published', 'description' => 'Jurnal Scopus/WoS'],
    ['proposal_type' => 'penelitian', 'category' => 'HKI', 'group' => 'Paten', 'type' => 'Paten Sederhana', 'target_status' => 'Granted', 'description' => 'Sertifikat Paten Sederhana'],
    // PENGABDIAN
    ['proposal_type' => 'pengabdian', 'category' => 'Pemberdayaan', 'group' => 'Pelatihan', 'type' => 'Capacity Building', 'target_status' => 'Certified', 'description' => 'Sertifikat Pelatihan Masyarakat'],
    // ... more
];
```

---

## 11. Testing Checklist

After implementing improvements:

- [ ] Seed all master data successfully
- [ ] Create Penelitian proposal with TKT fields
- [ ] Create Pengabdian proposal without TKT (should hide)
- [ ] Budget percentages validated correctly
- [ ] Output types show correct options per proposal type
- [ ] MOU upload works for Pengabdian partners
- [ ] Progress reports can be submitted
- [ ] DRAFT proposals can be edited, SUBMITTED cannot
- [ ] Science clusters show OECD codes correctly
- [ ] PRN descriptions display in forms
- [ ] Budget calculator shows live percentages

---

## 12. Documentation Updates Needed

- [ ] Update `DATA-STRUCTURES.md` with output type taxonomy
- [ ] Update `WORKFLOWS.md` with draft→submitted flow
- [ ] Update `MASTER-DATA.md` with budget percentage limits
- [ ] Create `API-INTEGRATION.md` for future SINTA/PDDIKTI sync
- [ ] Update `README.md` with BIMA alignment status

---

## Conclusion

The current SIM LPPM ITSNU implementation is **solid and well-structured**, with most core features already in place. The main improvements needed are:

1. **Master data enrichment** (descriptions, codes, taxonomy)
2. **Budget validation** (percentage limits per BIMA)
3. **Output type standardization** (create master table)
4. **Eligibility tracking** (SINTA/homebase fields)
5. **UI enhancements** (type selection, budget calculator, progress indicators)

**Priority:** Focus on **Phase 1 & 2** (migrations + validation) to ensure immediate BIMA compliance.

---

**Document End**
