<?php

namespace Database\Seeders;

use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use Illuminate\Database\Seeder;

class BudgetComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hon = BudgetGroup::where('code', 'HON')->first();
        $per = BudgetGroup::where('code', 'PER')->first();
        $bhp = BudgetGroup::where('code', 'BHP')->first();
        $prj = BudgetGroup::where('code', 'PRJ')->first();
        $lan = BudgetGroup::where('code', 'LAN')->first();

        // Honorarium Components
        $components = [
            ['budget_group_id' => $hon->id, 'code' => 'HON01', 'name' => 'Ketua Peneliti', 'unit' => 'bulan', 'description' => 'Honor Ketua Peneliti untuk 1 tahun'],
            ['budget_group_id' => $hon->id, 'code' => 'HON02', 'name' => 'Anggota Peneliti', 'unit' => 'bulan', 'description' => 'Honor Anggota Peneliti per bulan'],
            ['budget_group_id' => $hon->id, 'code' => 'HON03', 'name' => 'Narasumber/Pembicara', 'unit' => 'kali', 'description' => 'Fee narasumber untuk seminar atau workshop'],
            ['budget_group_id' => $hon->id, 'code' => 'HON04', 'name' => 'Enumerator/Surveyor', 'unit' => 'orang', 'description' => 'Honor surveyor untuk pengumpulan data'],
            ['budget_group_id' => $hon->id, 'code' => 'HON05', 'name' => 'Operator/Admin', 'unit' => 'bulan', 'description' => 'Honor operator dan administrasi'],

            // Peralatan Penunjang
            ['budget_group_id' => $per->id, 'code' => 'PER01', 'name' => 'Perangkat Komputer/Laptop', 'unit' => 'unit', 'description' => 'Pembelian atau sewa laptop untuk penelitian'],
            ['budget_group_id' => $per->id, 'code' => 'PER02', 'name' => 'Software/Aplikasi', 'unit' => 'paket', 'description' => 'Lisensi software analisis data dan aplikasi pendukung'],
            ['budget_group_id' => $per->id, 'code' => 'PER03', 'name' => 'Peralatan Laboratorium', 'unit' => 'unit', 'description' => 'Pembelian peralatan spesifik untuk penelitian'],
            ['budget_group_id' => $per->id, 'code' => 'PER04', 'name' => 'Kamera/Recorder', 'unit' => 'unit', 'description' => 'Peralatan dokumentasi dan perekaman'],
            ['budget_group_id' => $per->id, 'code' => 'PER05', 'name' => 'Alat Ukur/Instrumen', 'unit' => 'unit', 'description' => 'Peralatan ukur dan instrumen penelitian'],

            // Bahan Habis Pakai
            ['budget_group_id' => $bhp->id, 'code' => 'BHP01', 'name' => 'ATK (Alat Tulis Kantor)', 'unit' => 'pack', 'description' => 'Kertas, tinta, pena, dan alat tulis lainnya'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP02', 'name' => 'Fotokopi dan Penjilidan', 'unit' => 'lembar', 'description' => 'Biaya fotokopi dokumen dan penjilidan laporan'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP03', 'name' => 'Konsumsi Rapat/FGD', 'unit' => 'porsi', 'description' => 'Makanan dan minuman untuk rapat atau FGD'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP04', 'name' => 'Bahan Kimia/Reagensia', 'unit' => 'ml', 'description' => 'Bahan kimia dan reagensia untuk penelitian'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP05', 'name' => 'Material Penelitian', 'unit' => 'kg', 'description' => 'Bahan material yang dibutuhkan untuk penelitian'],

            // Perjalanan
            ['budget_group_id' => $prj->id, 'code' => 'PRJ01', 'name' => 'Transport Lokal', 'unit' => 'kali', 'description' => 'Transport untuk survei dan pengumpulan data lokal'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ02', 'name' => 'Transport Luar Kota', 'unit' => 'orang', 'description' => 'Tiket транспорт untuk perjalanan luar kota'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ03', 'name' => 'Akomodasi/Penginapan', 'unit' => 'malam', 'description' => 'Biaya hotel atau penginapan untuk perjalanan'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ04', 'name' => 'Uang Harian (Per Diem)', 'unit' => 'hari', 'description' => 'Uang harian untuk durante perjalanan dinas'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ05', 'name' => 'Sewa Kendaraan', 'unit' => 'hari', 'description' => 'Sewa kendaraan untuk mobilitas penelitian'],

            // Lain-lain
            ['budget_group_id' => $lan->id, 'code' => 'LAN01', 'name' => 'Biaya Publikasi Jurnal', 'unit' => 'artikel', 'description' => 'Biaya publikasi artikel ilmiah di jurnal'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN02', 'name' => 'Biaya Seminar/Konferensi', 'unit' => 'kali', 'description' => 'Biaya pendaftaran seminar atau konferensi'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN03', 'name' => 'Pengurusan HKI/Paten', 'unit' => 'sertifikat', 'description' => 'Biaya pengurusan hak kekayaan intelektual'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN04', 'name' => 'Pembuatan Laporan', 'unit' => 'dokumen', 'description' => 'Biaya cetak dan penjilidan laporan akhir'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN05', 'name' => 'Biaya Administrasi', 'unit' => 'paket', 'description' => 'Biaya administrasi dan dokumentasi penelitian'],
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
    }
}
