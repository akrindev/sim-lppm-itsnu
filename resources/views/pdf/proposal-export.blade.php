<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Proposal Export - {{ $proposal->id }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            color: #000;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 5px;
            padding-bottom: 5px;
        }
        .logo {
            width: 60px;
        }
        .header-text {
            text-align: left;
            padding-left: 10px;
        }
        .header-text div {
            font-weight: bold;
            font-size: 11pt;
        }
        .protection-box {
            text-align: center;
            border: 1px solid #000;
            padding: 5px;
            margin-top: 5px;
            font-size: 8pt;
            background-color: #fff;
            margin-bottom: 10px;
        }
        .proposal-type-box {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #000;
            color: #fff;
            padding: 3px;
            font-size: 10pt;
        }
        .proposal-id {
            text-align: center;
            font-size: 9pt;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            font-size: 8pt;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .no-border, .no-border td, .no-border th {
            border: none !important;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .page-break { page-break-after: always; }
        
        .section-title {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 3px;
            font-size: 10pt;
        }
        .mb-0 { margin-bottom: 0; }
        .mt-0 { margin-top: 0; }
        .group-total {
            background-color: #f9f9f9;
            font-weight: bold;
            padding: 5px;
            border: 1px solid #000;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <table class="header-table no-border">
        <tr>
            <td class="logo" style="width: 60px;">
                @if(file_exists(public_path('logo.png')))
                    <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 50px;">
                @endif
            </td>
            <td class="header-text">
                <div>Kementerian Pendidikan, Kebudayaan, Riset dan Teknologi</div>
                <div>Jalan Jenderal Sudirman, Senayan, Jakarta Pusat 10270</div>
                <div style="font-weight: normal; font-size: 9pt;">https://bima.kemdikbud.go.id</div>
            </td>
        </tr>
    </table>

    <div class="protection-box">
        <strong>PROTEKSI ISI PROPOSAL</strong><br>
        Dilarang menyalin, menyimpan, memperbanyak sebagian atau seluruh isi proposal ini dalam bentuk apapun<br>
        kecuali oleh pengusul dan pengelola administrasi pengabdian kepada masyarakat
    </div>

    <div class="proposal-type-box">
        PROPOSAL {{ $proposal->detailable_type === 'App\Models\Research' ? 'PENELITIAN' : 'PENGABDIAN' }} {{ $proposal->start_year }}
    </div>

    <div class="proposal-id">
        ID Proposal: {{ $proposal->id }}<br>
        Rencana Pelaksanaan {{ $proposal->detailable_type === 'App\Models\Research' ? 'Penelitian' : 'Pengabdian' }} : tahun {{ $proposal->start_year }} s.d. tahun {{ (int)$proposal->start_year + (int)$proposal->duration_in_years - 1 }}
    </div>

    <div class="section-title">1. JUDUL {{ $proposal->detailable_type === 'App\Models\Research' ? 'PENELITIAN' : 'PENGABDIAN' }}</div>
    <p style="margin-left: 20px; font-weight: bold; text-align: justify; font-size: 10pt;" class="mt-0">{{ $proposal->title }}</p>

    <table>
        <thead>
            <tr>
                <th>Kelompok Skema</th>
                <th>Ruang Lingkup</th>
                <th>Bidang Fokus</th>
                <th>Lama Kegiatan</th>
                <th>Tahun Pertama Usulan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $proposal->researchScheme->name ?? '-' }}</td>
                <td class="text-center">
                    @if($proposal->detailable_type === 'App\Models\Research')
                        Penelitian
                    @else
                        Pemberdayaan Kemitraan Masyarakat
                    @endif
                </td>
                <td class="text-center">{{ $proposal->focusArea->name ?? '-' }}</td>
                <td class="text-center">{{ $proposal->duration_in_years }}</td>
                <td class="text-center">{{ $proposal->start_year }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">2. IDENTITAS PENGUSUL</div>
    <table>
        <thead>
            <tr>
                <th>Nama, Peran</th>
                <th>Perguruan Tinggi/ Institusi</th>
                <th>Program Studi/ Bagian</th>
                <th>Bidang Tugas</th>
                <th>ID Sinta</th>
                <th>H-Index</th>
                <th>Rumpun Ilmu</th>
            </tr>
        </thead>
        <tbody>
            {{-- Ketua --}}
            <tr>
                <td>
                    <span class="font-bold">{{ strtoupper($proposal->submitter->name) }}</span><br>
                    Ketua Pengusul
                </td>
                <td>{{ $proposal->submitter->identity->institution->name ?? '-' }}</td>
                <td>{{ $proposal->submitter->identity->studyProgram->name ?? '-' }}</td>
                <td>{{ $proposal->detailable_type === 'App\Models\Research' ? 'Ketua Peneliti' : 'Ketua Pelaksana' }}</td>
                <td class="text-center">{{ $proposal->submitter->identity->sinta_id ?? '-' }}</td>
                <td class="text-center">-</td>
                <td>{{ $proposal->clusterLevel1->name ?? '-' }}</td>
            </tr>
            {{-- Anggota Dosen --}}
            @foreach($proposal->teamMembers->where('pivot.role', 'anggota') as $member)
                @if($member->identity?->type === 'dosen')
                <tr>
                    <td>
                        <span class="font-bold">{{ strtoupper($member->name) }}</span><br>
                        Anggota Pelaksana
                    </td>
                    <td>{{ $member->identity->institution->name ?? '-' }}</td>
                    <td>{{ $member->identity->studyProgram->name ?? '-' }}</td>
                    <td>{{ $member->pivot->tasks ?? '-' }}</td>
                    <td class="text-center">{{ $member->identity->sinta_id ?? '-' }}</td>
                    <td class="text-center">-</td>
                    <td>{{ $member->identity->studyProgram->name ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="section-title">3. IDENTITAS MAHASISWA</div>
    <table>
        <thead>
            <tr>
                <th>Nama, Peran</th>
                <th>NIM</th>
                <th>Perguruan Tinggi/ Institusi</th>
                <th>Program Studi/ Bagian</th>
                <th>Bidang Tugas</th>
            </tr>
        </thead>
        <tbody>
            @php $hasMahasiswa = false; @endphp
            @foreach($proposal->teamMembers as $member)
                @if($member->identity?->type === 'mahasiswa')
                @php $hasMahasiswa = true; @endphp
                <tr>
                    <td>
                        <span class="font-bold">{{ strtoupper($member->name) }}</span><br>
                        Mahasiswa
                    </td>
                    <td>{{ $member->identity->identity_id ?? '-' }}</td>
                    <td>{{ $member->identity->institution->name ?? '-' }}</td>
                    <td>{{ $member->identity->studyProgram->name ?? '-' }}</td>
                    <td>{{ $member->pivot->tasks ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
            @if(!$hasMahasiswa)
                <tr>
                    <td colspan="5" class="text-center">Tidak ada anggota mahasiswa</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($proposal->partners->count() > 0)
    <div class="section-title">4. MITRA KERJASAMA</div>
    @foreach($proposal->partners as $index => $partner)
    <div style="margin-bottom: 5px;">
        <strong>Mitra Sasaran {{ $index + 1 }}</strong>
        <table class="no-border" style="margin-left: 15px; margin-bottom: 5px;">
            <tr><td width="150" style="padding: 1px;">Jenis Mitra</td><td style="padding: 1px;">: {{ $partner->type ?? '-' }}</td></tr>
            <tr><td style="padding: 1px;">Nama Mitra Sasaran</td><td style="padding: 1px;">: {{ $partner->name }}</td></tr>
            <tr><td style="padding: 1px;">Institusi</td><td style="padding: 1px;">: {{ $partner->institution ?? '-' }}</td></tr>
            <tr><td style="padding: 1px;">Alamat Lengkap</td><td style="padding: 1px;">: {{ $partner->address ?? '-' }}</td></tr>
        </table>
    </div>
    @endforeach
    @endif

    <div class="page-break"></div>

    <div class="section-title">8. LUARAN DIJANJIKAN</div>
    <table>
        <thead>
            <tr>
                <th>Tahun</th>
                <th>Kelompok Luaran</th>
                <th>Jenis Luaran</th>
                <th>Status Target</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proposal->outputs as $output)
            <tr>
                <td class="text-center">{{ $output->output_year }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $output->group)) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $output->type)) }}</td>
                <td class="text-center">{{ $output->target_status }}</td>
                <td>{{ $output->description ?? '-' }}</td>
            </tr>
            @endforeach
            @if($proposal->outputs->isEmpty())
            <tr><td colspan="5" class="text-center">Belum ada luaran yang dijanjikan</td></tr>
            @endif
        </tbody>
    </table>

    <div class="section-title">11. ANGGARAN</div>
    <p class="mb-0">Rencana Anggaran Biaya pengabdian mengacu pada PMK dan buku Panduan Penelitian dan Pengabdian kepada Masyarakat yang berlaku.</p>
    @php
        $totalRAB = $proposal->budgetItems->sum('total_price');
        $budgetGroups = $proposal->budgetItems->groupBy(function($item) {
            return $item->budgetGroup->name ?? ($item->group ?? 'Lainnya');
        });
    @endphp
    <p class="mt-0"><strong>Total RAB : Rp. {{ number_format($totalRAB, 0, ',', '.') }}</strong></p>

    @foreach($budgetGroups as $groupName => $items)
        @php $groupTotal = $items->sum('total_price'); @endphp
        <div class="group-total">
            Total Biaya {{ $groupName }} Rp. {{ number_format($groupTotal, 0, ',', '.') }} 
            ({{ $totalRAB > 0 ? number_format(($groupTotal / $totalRAB) * 100, 2) : 0 }}%)
        </div>
        <table>
            <thead>
                <tr>
                    <th width="20%">Komponen</th>
                    <th width="30%">Item</th>
                    <th width="10%">Satuan</th>
                    <th width="10%">Vol.</th>
                    <th width="15%">Biaya Satuan</th>
                    <th width="15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->budgetComponent->name ?? $item->component }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td class="text-center">{{ $item->budgetComponent->unit ?? '-' }}</td>
                    <td class="text-center">{{ $item->volume }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
