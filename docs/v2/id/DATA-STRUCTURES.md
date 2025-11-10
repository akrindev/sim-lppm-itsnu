# Struktur Data v2.0 (Bahasa Indonesia)
## SIM LPPM ITSNU – Perbedaan Penelitian vs PKM

**Versi Dokumen:** 2.0  
**Terakhir Diperbarui:** 2025-11-09  
**Relasi Polimorfik:** `proposals.detailable` → `research` ATAU `community_services`

---

## Ikhtisar Relasi Polimorfik

### Struktur Tabel Proposal

```php
proposals {
    detailable_type: 'App\Models\Research' | 'App\Models\CommunityService'
    detailable_id: UUID (FK ke research.id atau community_services.id)
}
```

### Implementasi Relasi

```php
// Model Proposal
public function detailable(): MorphTo
{
    return $this->morphTo();
}

// Model Research
public function proposal(): MorphOne
{
    return $this->morphOne(Proposal::class, 'detailable');
}

// Model CommunityService
public function proposal(): MorphOne
{
    return $this->morphOne(Proposal::class, 'detailable');
}
```

---

## Data Proposal Penelitian (Research)

### Tabel: `research`

**Kolom Wajib:**

| Kolom | Tipe | Wajib | Deskripsi |
|-------|------|-------|-----------|
| id | uuid | ✅ | Primary key |
| macro_research_group_id | bigint | ✅ | FK kategori riset |
| final_tkt_target | integer | ✅ | Level TKT (0-9) |
| background | longText | ✅ | Latar belakang riset |
| state_of_the_art | longText | ✅ | Tinjauan pustaka |
| methodology | longText | ✅ | Metode penelitian |
| roadmap_data | json | ✅ | Roadmap multi-tahun |
| substance_file | string | ❌ | Dokumen substansi |

### TKT (Technology Readiness Level)

**Skala:** 0-9

| Level | Deskripsi |
|-------|-----------|
| 0-2 | Riset dasar (konsep, prinsip) |
| 3-4 | Riset terapan (proof-of-concept, prototipe) |
| 5-6 | Pengembangan (pilot, demonstrasi) |
| 7-9 | Implementasi (produksi, komersialisasi) |

**Penggunaan di Penelitian:**
- `final_tkt_target`: target TKT saat selesai  
- Dilacak pada `roadmap_data` per tahun  
- Diukur pada laporan kemajuan

### Struktur JSON Roadmap

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

**Validasi:**
- Minimal memiliki `year_1`
- Tiap tahun wajib `activities`, `targets`, `tkt_level`
- `tkt_level` harus progresif (year_2 >= year_1)

### State of the Art (Tinjauan Pustaka)

**Konten Wajib:** riset 5 tahun terakhir, temuan kunci & gap, kerangka teori, referensi metodologi, kebaruan riset.  
**Panjang:** 500-1000 kata.

### Metodologi (Metode Penelitian)

**Konten Wajib:** desain riset, pengumpulan data, sampling, teknik analisis, alat/instrumen, validitas & reliabilitas.  
**Panjang:** 300-700 kata.

### Kelompok Riset Makro

**Tabel:** `macro_research_groups`  
**Contoh:** Ilmu Komputer & Informatika, Teknik Elektro, Bioteknologi, Energi Terbarukan, Material & Nano.  
**Tujuan:** Klasifikasi riset tingkat makro untuk pelaporan institusi.

### Luaran Khusus Penelitian

Contoh: artikel jurnal (Q1-Q4), prosiding, paten (diajukan/disetujui), prototipe (berbasis TKT), buku/monograf, transfer teknologi.

---

## Data Proposal PKM (Community Service)

### Tabel: `community_services`

**Kolom Wajib:**

| Kolom | Tipe | Wajib | Deskripsi |
|-------|------|-------|-----------|
| id | uuid | ✅ | Primary key |
| partner_id | bigint | ❌ | FK mitra utama (opsional) |
| partner_issue_summary | text | ✅ | Permasalahan komunitas |
| solution_offered | text | ✅ | Solusi yang ditawarkan |

### Integrasi Mitra

- Mitra utama (opsional): `community_services.partner_id` → `partners.id` (satu mitra utama per proposal)  
- Banyak mitra: pivot `proposal_partner` (M:N)  
- Tipe mitra: NGO, Community, Government, School, SME

### Ringkasan Isu Mitra

Konten: deskripsi masalah, dampak & urgensi, pemangku kepentingan terdampak, analisis situasi, alasan penting.  
Panjang: 300-500 kata.  
Contoh disediakan pada versi Inggris (tetap relevan dalam Bahasa Indonesia).

### Solusi yang Ditawarkan

Konten: pendekatan intervensi, aktivitas & metode, outcome yang diharapkan, strategi pelibatan, rencana keberlanjutan.  
Panjang: 400-600 kata.  
Contoh disediakan pada versi Inggris (tetap relevan dalam Bahasa Indonesia).

### Luaran Khusus PKM

Contoh: program pemberdayaan, pelatihan, publikasi pengabdian, prototipe komunitas, layanan konsultasi, laporan dampak, sertifikat mitra.

---

## Tabel Perbandingan: Penelitian vs PKM

| Aspek | Penelitian (Research) | PKM (Pengabdian) |
|-------|------------------------|------------------|
| Tujuan Utama | Penemuan ilmiah, pembentukan pengetahuan | Penyelesaian masalah komunitas, dampak sosial |
| Fokus Luaran | Publikasi, paten, prototipe | Pemberdayaan, layanan |
| Mitra | Opsional | Wajib |
| Metodologi | Wajib (ketat) | Tidak wajib (aksi, fleksibel) |
| Pelacakan TKT | Wajib (0-9) | Tidak berlaku |
| Roadmap | Wajib (multi-tahun) | Tidak wajib (jadwal kegiatan cukup) |
| State of Art | Wajib | Tidak wajib |
| Latar Belakang | Wajib | Digantikan ringkasan isu mitra |
| Solusi | Implisit (metodologi) | Eksplisit (solution_offered) |
| Kelompok Riset Makro | Wajib | Tidak berlaku |
| Isu Mitra | Tidak berlaku | Wajib |
| Kriteria Evaluasi | Rigor ilmiah, kebaruan, kelayakan | Dampak sosial, manfaat, keberlanjutan |
| Durasi Tipikal | 1-3 tahun | 1-2 tahun |
| Fokus Anggaran | Peralatan, bahan lab, konferensi | Bahan pelatihan, aktivitas komunitas, perjalanan |

---

## Data Proposal Umum (Dibagi Bersama)

### 1) Anggota Tim (`proposal_user`)

```php
{
    "proposal_id": "uuid",
    "user_id": "uuid",
    "role": "ketua|anggota",
    "tasks": "Deskripsi tugas",
    "status": "pending|accepted|rejected"
}
```

### 2) Item Anggaran (`budget_items`)

```php
{
    "proposal_id": "uuid",
    "budget_group_id": "bigint",
    "budget_component_id": "bigint",
    "item_description": "text",
    "volume": "integer",
    "unit_price": "decimal(15,2)",
    "total_price": "decimal(15,2)"    // volume × unit_price (otomatis)
}
```

Perbedaan fokus anggaran: penelitian (lebih banyak peralatan/bahan/konferensi), PKM (lebih banyak pelatihan/aktivitas komunitas/perjalanan lokal).

### 3) Jadwal Kegiatan (`activity_schedules`)

```php
{
    "proposal_id": "uuid",
    "activity_name": "string",
    "year": "integer",
    "start_month": "integer (1-12)",
    "end_month": "integer (1-12)"
}
```

Granularitas: penelitian (tahunan, milestone besar), PKM (lebih sering: bulanan/mingguan).

### 4) Luaran Proposal (`proposal_outputs`)

```php
{
    "proposal_id": "uuid",
    "output_year": "integer",
    "category": "required|additional",
    "type": "string",
    "target_status": "string",
    "output_description": "text",
    "journal_name": "string",
    "estimated_date": "date"
}
```

Jenis luaran umum: penelitian (journal, patent, prototype, book, conference), PKM (workshop, training, consultation, video, article, certificate).

### 5) Tahapan Riset (`research_stages`)

Digunakan keduanya (meski bernama "research"): tahap berurutan dengan penanggung jawab.

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

### 6) Kata Kunci (`proposal_keyword`)

```php
{
    "proposal_id": "uuid",
    "keyword_id": "bigint"
}
```

### 7) Mitra (`proposal_partner`) – Keduanya Bisa

```php
{
    "proposal_id": "uuid",
    "partner_id": "bigint"
}
```

---

## Menentukan Tipe Proposal

### Di Kode

```php
$proposal = Proposal::find($id);

if ($proposal->detailable instanceof Research) {
    // Penelitian
    $tkt = $proposal->detailable->final_tkt_target;
    $methodology = $proposal->detailable->methodology;
    $roadmap = $proposal->detailable->roadmap_data;
} 
elseif ($proposal->detailable instanceof CommunityService) {
    // PKM
    $issue = $proposal->detailable->partner_issue_summary;
    $solution = $proposal->detailable->solution_offered;
    $partner = $proposal->detailable->partner;
}
```

### Di Livewire

```php
Route::get('/research/proposal/create', Research\Proposal\Create::class);
Route::get('/community-service/proposal/create', CommunityService\Proposal\Create::class);
```

Komponen terpisah: `app/Livewire/Research/Proposal/*` (Penelitian), `app/Livewire/CommunityService/Proposal/*` (PKM). Logika bersama di `app/Livewire/Forms/ProposalForm.php`.

---

## Perbedaan Validasi

### Validasi Penelitian

```php
'macro_research_group_id' => 'required|exists:macro_research_groups,id',
'final_tkt_target' => 'required|integer|min:0|max:9',
'background' => 'required|string|min:100',
'state_of_the_art' => 'required|string|min:100',
'methodology' => 'required|string|min:100',
'roadmap_data' => 'required|json',
```

### Validasi PKM

```php
'partner_issue_summary' => 'required|string|min:100',
'solution_offered' => 'required|string|min:100',
'partner_id' => 'nullable|exists:partners,id',
```

---

## Perbedaan Pelaporan Progres

Keduanya memakai `progress_reports`, namun fokus berbeda:

### Laporan Kemajuan Penelitian
- Progres TKT, hasil eksperimen, pembaruan analisis, status publikasi, perkembangan prototipe.  
- Luaran wajib: draft/submisi jurnal, pengajuan paten, milestone prototipe, presentasi konferensi.

### Laporan Kemajuan PKM
- Aktivitas (workshop, pelatihan), partisipasi komunitas, indikator dampak sosial, umpan balik mitra, keberlanjutan.  
- Luaran wajib: sertifikat, daftar hadir, testimoni, dokumentasi (foto/video), survei kepuasan mitra.

---

## Perbedaan UI

### Form Proposal Penelitian
Bagian: Informasi dasar, latar & tinjauan pustaka, metodologi & pendekatan, roadmap & target TKT, tim & tugas, anggaran, luaran (publikasi/paten), jadwal kegiatan.  
Tema warna: Biru/Navy (akademik).

### Form Proposal PKM
Bagian: Informasi dasar, konteks mitra, identifikasi masalah (isu mitra), desain solusi (solution offered), tim & tugas, anggaran, luaran (pelatihan/layanan), jadwal kegiatan.  
Tema warna: Hijau/Teal (komunitas).

---

## Contoh Query Database

```php
$researchProposals = Proposal::where('detailable_type', 'App\Models\Research')
    ->with('detailable.macroResearchGroup')
    ->get();

$pkmProposals = Proposal::where('detailable_type', 'App\Models\CommunityService')
    ->with('detailable.partner')
    ->get();
```

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

**Akhir Dokumen**
