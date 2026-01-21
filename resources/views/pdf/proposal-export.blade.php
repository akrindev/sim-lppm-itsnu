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
        
        .title-border-box {
            border: 1px solid #000;
            padding: 10px;
            margin-left: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: justify;
        }

        .group-total {
            font-weight: bold;
            padding: 5px 0;
            margin-bottom: 2px;
            font-size: 9pt;
        }
        a {
            color: #0000EE;
            text-decoration: underline;
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
                <div>Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</div>
                <div>Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan</div>
                <div style="font-weight: normal; font-size: 8pt;">Jl. Karangdowo No. 9, Karangdowo, Kec. Kedungwuni, Kab. Pekalongan, Jawa Tengah 51173</div>
                <div style="font-weight: normal; font-size: 8pt;">Email: lppmitsnupkl@gmail.com | Website: https://lppm.itsnupekalongan.ac.id/</div>
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

    {{-- 1. JUDUL --}}
    <div class="section-title">1. JUDUL {{ $proposal->detailable_type === 'App\Models\Research' ? 'PENELITIAN' : 'PENGABDIAN' }}</div>
    <div class="title-border-box">
        {{ $proposal->title }}
    </div>

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

    {{-- 2. IDENTITAS PENGUSUL --}}
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
                <td class="text-center">
                    @if($proposal->submitter->identity->sinta_id)
                        <a href="https://sinta.kemdikbud.go.id/authors/profile/{{ $proposal->submitter->identity->sinta_id }}" target="_blank">
                            {{ $proposal->submitter->identity->sinta_id }}
                        </a>
                    @else
                        -
                    @endif
                </td>
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
                    <td class="text-center">
                        @if($member->identity->sinta_id)
                            <a href="https://sinta.kemdikbud.go.id/authors/profile/{{ $member->identity->sinta_id }}" target="_blank">
                                {{ $member->identity->sinta_id }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">-</td>
                    <td>{{ $member->clusterLevel1->name ?? $member->identity->studyProgram->name ?? '-' }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- 3. IDENTITAS MAHASISWA --}}
    <div class="section-title">3. IDENTITAS MAHASISWA</div>
    @php 
        $mahasiswaMembers = $proposal->teamMembers->filter(fn($m) => $m->identity?->type === 'mahasiswa');
    @endphp
    @if($mahasiswaMembers->count() > 0)
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
            @foreach($mahasiswaMembers as $member)
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
            @endforeach
        </tbody>
    </table>
    @else
    <p style="margin-left: 20px;">Tidak ada anggota mahasiswa.</p>
    @endif

    {{-- 4. MITRA KERJASAMA --}}
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

    {{-- 5. Asta Cita (Skip if empty) --}}
    @if(isset($proposal->asta_cita) && $proposal->asta_cita)
    <div class="section-title">5. Asta Cita</div>
    <div style="margin-left: 20px; text-align: justify;">{{ $proposal->asta_cita }}</div>
    @endif

    {{-- 6. SDGs (Skip if empty) --}}
    @if(isset($proposal->sdgs) && $proposal->sdgs)
    <div class="section-title">6. (SDGs)</div>
    <div style="margin-left: 20px; text-align: justify;">{{ $proposal->sdgs }}</div>
    @endif

    {{-- 7. IKU (Skip if empty) --}}
    @if(isset($proposal->iku) && $proposal->iku)
    <div class="section-title">7. IKU</div>
    <div style="margin-left: 20px; text-align: justify;">{{ $proposal->iku }}</div>
    @endif

    {{-- 8. LUARAN DIJANJIKAN --}}
    @if($proposal->outputs->count() > 0)
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
        </tbody>
    </table>
    @endif

    {{-- 9. Dokumen Pendukung --}}
    @php
        $supportingDocs = [];
        if ($proposal->detailable?->hasMedia('substance_file')) {
            $supportingDocs[] = ['name' => 'Substansi Usulan', 'file' => $proposal->detailable->getFirstMedia('substance_file')];
        }
    @endphp
    @if(count($supportingDocs) > 0)
    <div class="section-title">9. Dokumen Pendukung</div>
    <table>
        <thead>
            <tr>
                <th>Nama Data Pendukung</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supportingDocs as $doc)
            <tr>
                <td>{{ $doc['name'] }}</td>
                <td>Terlampir</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- 10. Dokumen Pendukung Lainnya --}}
    @php
        $otherDocs = [];
        foreach($proposal->partners as $partner) {
            if ($partner->hasMedia('commitment_letter')) {
                $otherDocs[] = ['name' => 'Surat Pernyataan Kerjasama Mitra - ' . $partner->name, 'file' => $partner->getFirstMedia('commitment_letter')];
            }
        }
    @endphp
    @if(count($otherDocs) > 0)
    <div class="section-title">10. Dokumen Pendukung Lainnya</div>
    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Nama Mitra</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            @foreach($otherDocs as $doc)
            <tr>
                <td>Surat Pernyataan Kerjasama</td>
                <td>{{ str_replace('Surat Pernyataan Kerjasama Mitra - ', '', $doc['name']) }}</td>
                <td>Terlampir</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- 11. ANGGARAN --}}
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
                    <th width="35%">Item</th>
                    <th width="10%">Satuan</th>
                    <th width="5%">Vol.</th>
                    <th width="15%">Biaya Satuan</th>
                    <th width="15%">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->budgetComponent->name ?? $item->component }}</td>
                    <td>{{ $item->item_description }}</td>
                    <td class="text-center">{{ $item->budgetComponent->unit ?? ($item->unit ?? '-') }}</td>
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
