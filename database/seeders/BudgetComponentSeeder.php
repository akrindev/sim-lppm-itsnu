<?php

namespace Database\Seeders;

use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use Illuminate\Database\Seeder;

class BudgetComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Based on BIMA Kemdiktisaintek RAB Components 2026
     * Reference: Buku Panduan Penelitian dan Pengabdian kepada Masyarakat 2026
     */
    public function run(): void
    {
        // Get budget groups using BIMA-aligned codes
        $honor = BudgetGroup::where('code', 'HONOR')->first();
        $teknologi = BudgetGroup::where('code', 'TEKNOLOGI')->first();
        $pelatihan = BudgetGroup::where('code', 'PELATIHAN')->first();
        $perjalanan = BudgetGroup::where('code', 'PERJALANAN')->first();
        $lainnya = BudgetGroup::where('code', 'LAINNYA')->first();

        // Complete budget component structure aligned with BIMA standards
        $components = [
            // HONOR - Upah dan Jasa (Max 10%)
            ['budget_group_id' => $honor->id, 'code' => 'HON01', 'name' => 'Ketua Peneliti/Pengabdi', 'unit' => 'bulan', 'description' => 'Honor Ketua Peneliti atau Ketua Pengabdian'],
            ['budget_group_id' => $honor->id, 'code' => 'HON02', 'name' => 'Anggota Peneliti/Pengabdi', 'unit' => 'bulan', 'description' => 'Honor Anggota Peneliti atau Pengabdian'],
            ['budget_group_id' => $honor->id, 'code' => 'HON03', 'name' => 'Asisten Peneliti/Panel', 'unit' => 'bulan', 'description' => 'Honor Asisten tenaga ahli atau panel ahli'],
            ['budget_group_id' => $honor->id, 'code' => 'HON04', 'name' => 'Narasumber/Pembicara', 'unit' => 'kali', 'description' => 'Fee narasumber untuk seminar, workshop, atau FGD'],
            ['budget_group_id' => $honor->id, 'code' => 'HON05', 'name' => 'Enumerator/Surveyor', 'unit' => 'orang', 'description' => 'Honor enumerator untuk pengumpulan data lapangan'],
            ['budget_group_id' => $honor->id, 'code' => 'HON06', 'name' => 'Operator/Administrasi', 'unit' => 'bulan', 'description' => 'Honor operator data entry dan administrasi penelitian'],
            ['budget_group_id' => $honor->id, 'code' => 'HON07', 'name' => 'Editor/Formatter', 'unit' => 'dokumen', 'description' => 'Jasa editing dan formatting laporan'],
            ['budget_group_id' => $honor->id, 'code' => 'HON08', 'name' => 'Translator/Penerjemah', 'unit' => 'halaman', 'description' => 'Jasa penerjemahan dokumen atau publikasi'],

            // TEKNOLOGI & INOVASI - Bahan dan Peralatan (Min 50%)
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK01', 'name' => 'Perangkat Komputer/Laptop', 'unit' => 'unit', 'description' => 'Pembelian atau sewa laptop untuk penelitian/pengabdian'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK02', 'name' => 'Software/Aplikasi', 'unit' => 'paket', 'description' => 'Lisensi software analisis data dan aplikasi pendukung'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK03', 'name' => 'Peralatan Laboratorium', 'unit' => 'unit', 'description' => 'Pembelian peralatan spesifik laboratorium riset'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK04', 'name' => 'Kamera/Recording Device', 'unit' => 'unit', 'description' => 'Peralatan dokumentasi dan perekaman multimedia'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK05', 'name' => 'Alat Ukur/Instrumen', 'unit' => 'unit', 'description' => 'Peralatan ukur dan instrumen penelitian akurat'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK06', 'name' => 'Bahan Kimia/Reagensia', 'unit' => 'ml/gr', 'description' => 'Bahan kimia dan reagensia untuk analisis laboratorium'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK07', 'name' => 'Media Kultur/Bakteri', 'unit' => 'unit', 'description' => 'Media pertumbuhan dan kultur bakteri/yeast'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK08', 'name' => 'Sample/Spesimen Uji', 'unit' => 'sample', 'description' => 'Biaya pengambilan dan pengiriman sample uji'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK09', 'name' => 'Material/Komponen Elektronik', 'unit' => 'set', 'description' => 'Komponen untuk prototipe atau modifikasi peralatan'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK10', 'name' => 'Bahan 3D Printing/CNC', 'unit' => 'kg', 'description' => 'Material untuk fabrikasi prototipe dengan 3D printer/CNC'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK11', 'name' => 'Kit PCR/Reagen Diagnostik', 'unit' => 'kit', 'description' => 'Kit analisis PCR dan reagen diagnostik biologi'],
            ['budget_group_id' => $teknologi->id, 'code' => 'TEK12', 'name' => 'Sensor/IoT Device', 'unit' => 'unit', 'description' => 'Sensor dan perangkat IoT untuk monitoring data'],

            // PELATIHAN - Capacity Building (Max 20%)
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL01', 'name' => 'Workshop/Teknis', 'unit' => 'kali', 'description' => 'Workshop teknis untuk peningkatan kapasitas tim'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL02', 'name' => 'Pelatihan Software/Tools', 'unit' => 'sesi', 'description' => 'Pelatihan penggunaan software dan analis tools'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL03', 'name' => 'Seminar/Diseminasi', 'unit' => 'kali', 'description' => 'Seminar diseminasi hasil untuk stakeholders'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL04', 'name' => 'Pelatihan Mitra/Komunitas', 'unit' => 'sesi', 'description' => 'Pelatihan untuk mitra atau komunitas sasaran PKM'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL05', 'name' => 'Konsultasi Ahli', 'unit' => 'jam', 'description' => 'Konsultasi dengan tenaga ahli nasional/internasional'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL06', 'name' => 'Training of Trainers (ToT)', 'unit' => 'batch', 'description' => 'Pelatihan calon trainer untuk pengabdian berkelanjutan'],
            ['budget_group_id' => $pelatihan->id, 'code' => 'PEL07', 'name' => 'Study Banding/Kunjungan', 'unit' => 'kali', 'description' => 'Study banding ke institusi atau lokasi relevan'],

            // PERJALANAN - Field Work (Max 15%)
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ01', 'name' => 'Transportasi Lokal', 'unit' => 'kali', 'description' => 'Transport darat untuk survei dan pengumpulan data lokal'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ02', 'name' => 'Transportasi Antar Kota', 'unit' => 'tiket', 'description' => 'Tiket pesawat/kereta/bus antar kota'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ03', 'name' => 'Akomodasi/Penginapan', 'unit' => 'malam', 'description' => 'Biaya hotel/penginapan selama field work'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ04', 'name' => 'Uang Harian (Per Diem)', 'unit' => 'hari', 'description' => 'Uang saku harian selama perjalanan dinas'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ05', 'name' => 'Sewa Kendaraan', 'unit' => 'hari', 'description' => 'Sewa kendaraan operasional di lokasi riset'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ06', 'name' => 'Konsumsi Lapangan', 'unit' => 'orang', 'description' => 'Makanan/minuman selama survei lapangan'],
            ['budget_group_id' => $perjalanan->id, 'code' => 'PRJ07', 'name' => 'Biaya Masuk Lokasi', 'unit' => 'kali', 'description' => 'Tiket masuk objek wisata/konservasi/area riset'],

            // LAINNYA - Output & Administrasi (Max 5%)
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI01', 'name' => 'Publikasi Jurnal Internasional', 'unit' => 'artikel', 'description' => 'Biaya publikasi di jurnal internasional terakreditasi'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI02', 'name' => 'Publikasi Jurnal Nasional', 'unit' => 'artikel', 'description' => 'Biaya publikasi di jurnal nasional terakreditasi'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI03', 'name' => 'Seminar/Konferensi Nasional', 'unit' => 'kali', 'description' => 'Biaya registrasi seminar/konferensi nasional'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI04', 'name' => 'Seminar/Konferensi Internasional', 'unit' => 'kali', 'description' => 'Biaya registrasi seminar/konferensi internasional'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI05', 'name' => 'Pengurusan Hak Kekayaan Intelektual', 'unit' => 'sertifikat', 'description' => 'Biaya pengurusan paten, merek, atau hak cipta'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI06', 'name' => 'Penerbitan Buku/Monograf', 'unit' => 'eksemplar', 'description' => 'Biaya penerbitan buku hasil penelitian'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI07', 'name' => 'Cetak Laporan Akhir', 'unit' => 'eksemplar', 'description' => 'Biaya percetakan laporan akhir penelitian'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI08', 'name' => 'Fotokopi & Penjilidan', 'unit' => 'lembar', 'description' => 'Biaya fotokopi dan penjilidan dokumen'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI09', 'name' => 'ATK (Alat Tulis Kantor)', 'unit' => 'pack', 'description' => 'Kertas, tinta, pena, dan alat tulis kantor'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI10', 'name' => 'Biaya Administrasi', 'unit' => 'paket', 'description' => 'Biaya administrasi umum dan dokumentasi'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI11', 'name' => 'Asuransi Kesehatan Tim', 'unit' => 'orang', 'description' => 'Asuransi kesehatan selama penelitian lapangan'],
            ['budget_group_id' => $lainnya->id, 'code' => 'LAI12', 'name' => 'Biaya Notaris/Legal', 'unit' => 'dokumen', 'description' => 'Biaya legalisasi dokumen kerjasama/MOU'],
        ];

        foreach ($components as $component) {
            BudgetComponent::updateOrCreate(
                [
                    'budget_group_id' => $component['budget_group_id'],
                    'code' => $component['code'],
                ],
                $component
            );
        }

        $this->command->info('Budget components seeded successfully!');
        $this->command->info('Honor/Upah: 8 components');
        $this->command->info('Teknologi & Inovasi: 12 components');
        $this->command->info('Pelatihan: 7 components');
        $this->command->info('Perjalanan: 7 components');
        $this->command->info('Lainnya: 12 components');
        $this->command->info('Total: 46 budget components seeded');
    }
}
