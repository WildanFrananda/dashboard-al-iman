<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Raport - {{ $student['nama'] ?? 'Murid' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root { --ink:#1f2937; --muted:#6b7280; --line:#d1d5db; --brand:#0F609B; }

        body {
            font-family: "Times New Roman", Georgia, serif;
            color: var(--ink);
            background: #f3f4f6;
            font-size: 12px;
            line-height: 1.45;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            padding: 18mm 16mm;
            background: #fff;
            box-shadow: 0 2px 14px rgba(0,0,0,.12);
        }

        /* ---- KOP / HEADER ---- */
        .kop {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px double var(--ink);
            padding-bottom: 12px;
        }
        .kop img { height: 64px; width: auto; }
        .kop .txt { text-align: center; flex: 1; }
        .kop .txt h1 { font-size: 20px; letter-spacing: .5px; text-transform: uppercase; }
        .kop .txt h2 { font-size: 15px; font-weight: normal; }
        .kop .txt p { font-size: 11px; color: var(--muted); }

        .doc-title {
            text-align: center;
            margin: 16px 0 14px;
        }
        .doc-title h3 { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; text-decoration: underline; }
        .doc-title span { font-size: 12px; color: var(--muted); }

        /* ---- IDENTITAS ---- */
        .bio { width: 100%; margin-bottom: 14px; font-size: 12px; }
        .bio td { padding: 2px 0; vertical-align: top; }
        .bio .label { width: 130px; }
        .bio .sep { width: 14px; }
        .bio .val { font-weight: bold; }

        /* ---- TABEL NILAI ---- */
        table.grades { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.grades th, table.grades td { border: 1px solid var(--line); padding: 7px 8px; }
        table.grades thead th {
            background: #eef2f7;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        table.grades td { font-size: 12px; }
        .c { text-align: center; }
        .subj { font-weight: 600; }
        .na { font-weight: bold; color: var(--brand); background: #f0f7ff; }
        .below { color: #b91c1c; }
        .muted { color: var(--muted); }

        /* ---- RINGKASAN ---- */
        .summary {
            display: flex;
            gap: 24px;
            margin: 10px 0 4px;
            font-size: 12px;
        }
        .summary b { color: var(--brand); }

        /* ---- TANDA TANGAN ---- */
        .sign {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 12px;
        }
        .sign .col { width: 32%; text-align: center; }
        .sign .space { height: 64px; }
        .sign .name { font-weight: bold; text-decoration: underline; }

        .empty { text-align: center; color: var(--muted); padding: 24px; font-style: italic; }

        /* ---- TOOLBAR (layar saja) ---- */
        .toolbar {
            position: sticky; top: 0;
            text-align: center; padding: 10px;
            background: #111827;
        }
        .toolbar button {
            background: var(--brand); color: #fff; border: 0;
            padding: 8px 22px; border-radius: 6px; font-size: 13px;
            cursor: pointer; font-family: system-ui, sans-serif; font-weight: 600;
        }
        .toolbar button:hover { background: #0c4a7a; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { margin: 0; box-shadow: none; width: auto; min-height: auto; padding: 0; }
            @page { size: A4; margin: 14mm; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <button type="button" onclick="window.print()">🖨 Cetak / Simpan PDF</button>
    </div>

    <div class="sheet">

        <!-- KOP -->
        <div class="kop">
            <img src="{{ asset('img/siakman.png') }}" alt="Logo">
            <div class="txt">
                <h1>Sekolah Al-Iman</h1>
                <h2>SIAKMAN — Sistem Akademik Al-Iman</h2>
                <p>Laporan Hasil Belajar Peserta Didik</p>
            </div>
        </div>

        <div class="doc-title">
            <h3>Rapor Nilai Murid</h3>
            <span>Semester {{ $semester === 1 ? 'Ganjil' : 'Genap' }} &middot; Tahun Ajaran {{ $tahunAjaran }}</span>
        </div>

        <!-- IDENTITAS -->
        <table class="bio">
            <tr>
                <td class="label">Nama Peserta Didik</td><td class="sep">:</td><td class="val">{{ $student['nama'] ?? '-' }}</td>
                <td class="label">Kelas</td><td class="sep">:</td><td class="val">{{ $student['kelas'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td><td class="sep">:</td><td class="val">{{ $student['nis'] ?? '-' }}</td>
                <td class="label">Semester</td><td class="sep">:</td><td class="val">{{ $semester === 1 ? 'Ganjil (1)' : 'Genap (2)' }}</td>
            </tr>
        </table>

        <!-- TABEL NILAI -->
        <table class="grades">
            <thead>
                <tr>
                    <th style="width:34px">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width:48px">KKM</th>
                    <th style="width:74px">Kehadiran</th>
                    <th style="width:48px">UTS</th>
                    <th style="width:48px">UAS</th>
                    <th style="width:70px">Nilai Akhir</th>
                    <th style="width:180px">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td class="c">{{ $i + 1 }}</td>
                        <td class="subj">{{ $r['subject'] }}</td>
                        <td class="c">{{ $r['kkm'] }}</td>
                        <td class="c">{{ $r['kehadiran'] !== null ? $r['kehadiran'].'%' : '—' }}</td>
                        <td class="c {{ $r['uts'] !== null && $r['uts'] < $r['kkm'] ? 'below' : '' }}">{{ $r['uts'] ?? '—' }}</td>
                        <td class="c {{ $r['uas'] !== null && $r['uas'] < $r['kkm'] ? 'below' : '' }}">{{ $r['uas'] ?? '—' }}</td>
                        <td class="c na {{ $r['nilai_akhir'] !== null && $r['nilai_akhir'] < $r['kkm'] ? 'below' : '' }}">{{ $r['nilai_akhir'] ?? '—' }}</td>
                        <td>{{ $r['keterangan'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty">Belum ada nilai untuk semester dan tahun ajaran ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- RINGKASAN -->
        @if(count($records) > 0)
            <div class="summary">
                <span>Rata-rata UTS: <b>{{ $summary['rata_uts'] ?? '—' }}</b></span>
                <span>Rata-rata UAS: <b>{{ $summary['rata_uas'] ?? '—' }}</b></span>
                <span>Jumlah Mata Pelajaran: <b>{{ $summary['mapel'] }}</b></span>
            </div>
        @endif

        <!-- TANDA TANGAN -->
        <div class="sign">
            <div class="col">
                Orang Tua / Wali
                <div class="space"></div>
                <div>(........................)</div>
            </div>
            <div class="col">
                Wali Kelas
                <div class="space"></div>
                <div>(........................)</div>
            </div>
            <div class="col">
                {{ $tanggalCetak }}<br>
                Kepala Sekolah
                <div class="space"></div>
                <div>(........................)</div>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    </script>
</body>
</html>
