i# Dokumentasi Database SIM-LPPM

## 📋 Gambaran

Dokumentasi ini menyediakan Diagram Entitas-Hubungan (ERD) komprehensif untuk sistem SIM-LPPM - platform manajemen penelitian akademik dan pengabdian kepada masyarakat berbasis Laravel.

## 📁 File Dokumentasi

### 📄 Dokumentasi Inti
- **`erd-documentation.md`** - ERD berbasis teks lengkap dengan penjelasan detail
- **`erd-mermaid.md`** - Diagram visual Mermaid untuk tampilan interaktif
- **`README-erd.md`** - File gambaran umum ini (yang sedang Anda baca!)

### 🔧 File Database
- **`migrations/`** - Semua 36 file migrasi yang dianalisis
- **`seeders/`** - Data sampel untuk testing dan development

## 🎯 Arsitektur Sistem

### Logika Bisnis Inti
1. **Proposal Penelitian** - Manajemen proyek penelitian akademik
2. **Pengabdian kepada Masyarakat** - Proyek kemitraan dan outreach masyarakat
3. **Taksonomi Hierarkis** - Klasifikasi Focus Areas → Themes → Topics
4. **Kolaborasi Tim** - Tim proyek multi-pengguna dengan peran dan tanggung jawab
5. **Manajemen Anggaran** - Perencanaan dan pelacakan keuangan detail
6. **Perencanaan Output** - Perencanaan publikasi dan milestone
7. **Siklus Proyek** - Draft → Submitted → Reviewed → Approved → Completed

### Teknologi Kunci
- **Laravel 12** - Framework PHP
- **MySQL/PostgreSQL** - Mesin database
- **Eloquent ORM** - Abstraksi database
- **Relasi Polymorphic** - Hubungan model dinamis
- **Spatie Laravel Permission** - Kontrol akses berbasis peran
- **Laravel Telescope** - Monitoring development

## 📊 Ringkasan Hubungan Entitas

### 🔑 Entitas Sentral: Proposals
Tabel `proposals` adalah jantung sistem, menghubungkan ke:
- **Users** (pengaju + anggota tim)
- **Research/CommunityService** (konten polymorphic)
- **Tabel referensi** (skema, prioritas, taksonomi)
- **Entitas pendukung** (anggaran, jadwal, output, tahapan)

### 🏗️ Pola Arsitektur
- **Single Table Inheritance** - Proposal Research vs Community Service
- **Taksonomi Hierarkis** - Sistem kategorisasi multi-level
- **Tabel Pivot** - Hubungan many-to-many (tim, kata kunci)
- **Relasi Polymorphic** - Tipe konten dinamis

## 🚀 Panduan Cepat untuk Developers

### Memahami Skema
1. Mulai dengan `erd-documentation.md` untuk struktur tabel detail
2. Gunakan `erd-mermaid.md` untuk visualisasi hubungan
3. Referensikan file model di `app/Models/` untuk relasi Eloquent

### Common Queries
```php
// Get proposal with all relationships
$proposal = Proposal::with([
    'submitter',
    'detailable', // Research or CommunityService
    'teamMembers', // Team members via pivot
    'keywords', // Keywords via pivot
    'outputs',
    'budgetItems',
    'focusArea.theme.topic' // Hierarchical taxonomy
])->find($id);

// Get research proposal details
$research = $proposal->detailable; // instanceof Research

// Get community service with partner
$communityService = $proposal->detailable; // instanceof CommunityService
$partner = $communityService->partner;
```

### Aturan Bisnis yang Perlu Diingat
- Setiap proposal adalah Research ATAU Community Service (tidak pernah keduanya)
- Hierarki taksonomi: Focus Areas berisi Themes, Themes berisi Topics
- Science clusters memiliki 3 level (hubungan parent-child)
- Anggota tim memiliki peran (ketua/anggota) dan tugas yang ditugaskan
- Nilai SBK dihitung berdasarkan parameter proposal

## 🔍 Tips Development

### Eager Loading untuk Performa
```php
// Selalu eager load relasi untuk menghindari query N+1
$proposals = Proposal::with([
    'submitter',
    'detailable',
    'researchScheme',
    'focusArea',
    'theme',
    'topic'
])->get();
```

### Penanganan Relasi Polymorphic
```php
// Periksa tipe proposal
if ($proposal->detailable instanceof Research) {
    // Tangani logika spesifik penelitian
    $background = $proposal->detailable->background;
} elseif ($proposal->detailable instanceof CommunityService) {
    // Tangani logika pengabdian masyarakat
    $partner = $communityService->partner;
}
```

### Manajemen Anggota Tim
```php
// Dapatkan ketua tim
$leader = $proposal->teamMembers()
    ->wherePivot('role', 'ketua')
    ->first();

// Dapatkan semua anggota tim dengan tugasnya
$team = $proposal->teamMembers()
    ->withPivot(['role', 'tasks'])
    ->get();
```

## 📈 Statistik Sistem

- **36 File Migrasi** - Struktur database lengkap
- **19 Model** - Representasi Eloquent
- **20+ Tipe Relasi** - Interkoneksi kompleks
- **Entitas Polymorphic** - 2 tipe proposal utama
- **Level Hierarki** - Sistem taksonomi 3-tier
- **Tabel Pivot** - 2 hubungan many-to-many

## 🧪 Testing Skema

### Seeder yang Tersedia
Jalankan perintah berikut untuk mengisi data test:
```bash
php artisan db:seed
```

Seeder yang tersedia:
- `UserSeeder` - User test dengan berbagai peran
- `ProposalSeeder` - Sample proposal dengan data lengkap
- `ResearchSeeder` - Konten spesifik penelitian
- Berbagai seeder tabel referensi

### Query Sampel untuk Validasi
```php
// Periksa jumlah proposal berdasarkan tipe
$researchCount = Proposal::where('detailable_type', Research::class)->count();
$serviceCount = Proposal::where('detailable_type', CommunityService::class)->count();

// Validasi hierarki taksonomi
$themesCount = Theme::whereHas('focusArea')->count();
$topicsCount = Topic::whereHas('theme.focusArea')->count();
```

## 🔧 Perintah Maintenance

### Pemformatan Kode (Laravel Pint)
```bash
vendor/bin/pint
```

### Testing Semua Komponen
```bash
php artisan test
```

### Ekspor Skema Database
```bash
php artisan schema:dump
```

## 📞 Dukungan & Pertanyaan

Dokumentasi ini dihasilkan dengan menganalisis semua migrasi dan model dalam codebase SIM-LPPM. Untuk pertanyaan tentang relasi spesifik atau logika bisnis, lihat:

1. **File model** di `app/Models/` untuk relasi Eloquent
2. **File migrasi** di `database/migrations/` untuk struktur tabel
3. **Seeder** di `database/seeders/` untuk pola data sampel

## 🎨 Visual Aids

### View the ERD Online
- Copy the Mermaid code from `erd-mermaid.md` to [mermaid.live](https://mermaid.live)
- Install VS Code "Mermaid Preview" extension for inline viewing
- GitHub/GitLab automatically render Mermaid diagrams in markdown files

### Diagram Color Legend
- 🟦 **Primary Entities** (Users, Proposals)
- 🟩 **Content Entities** (Research, CommunityService)
- 🟨 **Reference Entities** (Taxonomy, Schemes)
- 🟪 **Pivot Entities** (Relationship tables)
- 🟥 **Supporting Entities** (Budget, Schedule, etc.)

---

*Generated on: October 26, 2025*
*System: SIM-LPPM (Laravel 12)*
*Total Tables: 25+*
*Total Relationships: 50+
