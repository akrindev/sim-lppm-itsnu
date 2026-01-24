<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Catatan Harian - {{ $proposal->id }}</title>
        <style>
            @page {
                margin: 1.5cm;
            }

            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 9pt;
                line-height: 1.4;
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

            .document-title {
                text-align: center;
                margin: 20px 0;
                font-weight: bold;
                font-size: 14pt;
                text-transform: uppercase;
                text-decoration: underline;
            }

            .info-table {
                width: 100%;
                margin-bottom: 20px;
            }

            .info-table td {
                border: none;
                padding: 2px 0;
                vertical-align: top;
            }

            .info-label {
                width: 150px;
                font-weight: bold;
            }

            .info-colon {
                width: 10px;
            }

            table.data-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            table.data-table th,
            table.data-table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
                vertical-align: top;
            }

            table.data-table th {
                background-color: #f2f2f2;
                text-align: center;
                font-weight: bold;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .font-bold {
                font-weight: bold;
            }

            .footer {
                margin-top: 30px;
                text-align: right;
                /* page-break-inside: avoid; */
            }

            .signature-space {
                height: 80px;
            }
        </style>
    </head>

    <body>
        <table class="header-table no-border">
            <tr>
                <td class="logo" style="width: 60px; border: none;">
                    @if (file_exists(public_path('logo.png')))
                        <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 50px;">
                    @endif
                </td>
                <td class="header-text" style="border: none;">
                    <div>Lembaga Penelitian dan Pengabdian kepada Masyarakat (LPPM)</div>
                    <div>Institut Teknologi dan Sains Nahdlatul Ulama (ITSNU) Pekalongan</div>
                    <div style="font-weight: normal; font-size: 8pt;">Jl. Karangdowo No. 9, Karangdowo, Kec. Kedungwuni,
                        Kab. Pekalongan, Jawa Tengah 51173</div>
                    <div style="font-weight: normal; font-size: 8pt;">Email: lppmitsnupkl@gmail.com | Website:
                        https://lppm.itsnupekalongan.ac.id/</div>
                </td>
            </tr>
        </table>

        <div class="document-title">
            CATATAN HARIAN (LOGBOOK)
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Judul Usulan</td>
                <td class="info-colon">:</td>
                <td><strong>{{ $proposal->title }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Ketua Pengusul</td>
                <td class="info-colon">:</td>
                <td>{{ $proposal->submitter->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Program Studi</td>
                <td class="info-colon">:</td>
                <td>{{ $proposal->submitter->identity->studyProgram->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Skema</td>
                <td class="info-colon">:</td>
                <td>{{ $proposal->researchScheme->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tahun Pelaksanaan</td>
                <td class="info-colon">:</td>
                <td>{{ $proposal->start_year }}</td>
            </tr>
        </table>



        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th>Aktivitas & Catatan</th>
                    <th width="15%">Kelompok RAB</th>
                    <th width="15%">Nominal (Rp)</th>
                    <th width="8%">Progres</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $index => $note)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $note->activity_date->format('d/m/Y') }}</td>
                        <td>
                            <div class="font-bold">{{ $note->activity_description }}</div>
                            @if ($note->notes)
                                <div style="margin-top: 5px; font-style: italic; color: #444; font-size: 8pt;">
                                    Catatan: {{ $note->notes }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $note->budgetGroup->name ?? '-' }}
                        </td>
                        <td class="text-right">
                            {{ $note->amount ? number_format($note->amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="text-center">{{ $note->progress_percentage }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada catatan aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- @if ($notes->count() > 0)
                <tfoot>
                    <tr>
                        <td colspan="4" class="font-bold text-right">Total Nominal Digunakan:</td>
                        <td class="font-bold text-right">
                            {{ number_format($notes->sum('amount'), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif --}}
        </table>

        @if ($notes->count() > 0)
            <div style="margin-top: 20px; margin-bottom: 10px; page-break-inside: avoid;">
                <div class="font-bold" style="margin-bottom: 5px;">Ringkasan Penggunaan Anggaran:</div>
                <table class="data-table" style="width: 50%;">
                    <thead>
                        <tr>
                            <th>Kelompok RAB</th>
                            <th class="text-right">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupTotals = $notes->groupBy('budget_group_id');
                        @endphp
                        @foreach ($groupTotals as $groupId => $items)
                            <tr>
                                <td>{{ $items->first()->budgetGroup->name ?? 'Tanpa Kelompok' }}</td>
                                <td class="text-right">{{ number_format($items->sum('amount'), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right">Total Keseluruhan</th>
                            <th class="text-right">{{ number_format($notes->sum('amount'), 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <div class="footer">
            <p>Pekalongan, {{ date('d F Y') }}</p>
            <p>Ketua Pengusul,</p>
            <div class="signature-space"></div>
            <p><strong>( {{ $proposal->submitter->name }} )</strong></p>
        </div>
    </body>

</html>
