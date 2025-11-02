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
            ['budget_group_id' => $hon->id, 'code' => 'HON01', 'name' => 'Ketua Peneliti'],
            ['budget_group_id' => $hon->id, 'code' => 'HON02', 'name' => 'Anggota Peneliti'],
            ['budget_group_id' => $hon->id, 'code' => 'HON03', 'name' => 'Narasumber/Pembicara'],
            ['budget_group_id' => $hon->id, 'code' => 'HON04', 'name' => 'Enumerator/Surveyor'],
            ['budget_group_id' => $hon->id, 'code' => 'HON05', 'name' => 'Operator/Admin'],

            // Peralatan Penunjang
            ['budget_group_id' => $per->id, 'code' => 'PER01', 'name' => 'Perangkat Komputer/Laptop'],
            ['budget_group_id' => $per->id, 'code' => 'PER02', 'name' => 'Software/Aplikasi'],
            ['budget_group_id' => $per->id, 'code' => 'PER03', 'name' => 'Peralatan Laboratorium'],
            ['budget_group_id' => $per->id, 'code' => 'PER04', 'name' => 'Kamera/Recorder'],
            ['budget_group_id' => $per->id, 'code' => 'PER05', 'name' => 'Alat Ukur/Instrumen'],

            // Bahan Habis Pakai
            ['budget_group_id' => $bhp->id, 'code' => 'BHP01', 'name' => 'ATK (Alat Tulis Kantor)'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP02', 'name' => 'Fotokopi dan Penjilidan'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP03', 'name' => 'Konsumsi Rapat/FGD'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP04', 'name' => 'Bahan Kimia/Reagensia'],
            ['budget_group_id' => $bhp->id, 'code' => 'BHP05', 'name' => 'Material Penelitian'],

            // Perjalanan
            ['budget_group_id' => $prj->id, 'code' => 'PRJ01', 'name' => 'Transport Lokal'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ02', 'name' => 'Transport Luar Kota'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ03', 'name' => 'Akomodasi/Penginapan'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ04', 'name' => 'Uang Harian (Per Diem)'],
            ['budget_group_id' => $prj->id, 'code' => 'PRJ05', 'name' => 'Sewa Kendaraan'],

            // Lain-lain
            ['budget_group_id' => $lan->id, 'code' => 'LAN01', 'name' => 'Biaya Publikasi Jurnal'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN02', 'name' => 'Biaya Seminar/Konferensi'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN03', 'name' => 'Pengurusan HKI/Paten'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN04', 'name' => 'Pembuatan Laporan'],
            ['budget_group_id' => $lan->id, 'code' => 'LAN05', 'name' => 'Biaya Administrasi'],
        ];

        foreach ($components as $component) {
            BudgetComponent::create($component);
        }
    }
}
