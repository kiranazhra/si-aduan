<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }} - {{ $unit }}</title>
    <style>
        @page { size: A4 landscape; margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 24px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #0f172a; background: #fff; }

        .bar { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 16px; }
        .bar button { font: inherit; font-weight: 600; padding: 8px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; }
        .bar button.utama { background: #0f2e5a; color: #fff; border-color: #0f2e5a; }

        /* ===== Kop Surat Resmi ===== */
        .kop { display: flex; align-items: center; gap: 16px; }
        .kop img { height: 72px; width: auto; flex-shrink: 0; }
        .kop .teks { flex: 1; text-align: center; color: #000; }
        .kop .instansi { font-size: 13px; font-weight: 700; letter-spacing: .2px; }
        .kop .dinas { font-size: 15px; font-weight: 800; letter-spacing: .2px; }
        .kop .satker { font-size: 16px; font-weight: 800; letter-spacing: .3px; }
        .kop .alamat { font-size: 10px; margin-top: 3px; color: #1f2937; }

        .garis-tebal { border-bottom: 3px solid #000; margin-top: 10px; }
        .garis-tipis { border-bottom: 1px solid #000; margin-bottom: 14px; }

        h1 { font-size: 15px; margin: 4px 0 2px; color: #000; text-align: center; text-transform: uppercase; text-decoration: underline; letter-spacing: .3px; }
        .subjudul { text-align: center; font-size: 11px; color: #334155; margin-bottom: 14px; }

        .info-laporan { border-collapse: collapse; margin-bottom: 14px; font-size: 12px; }
        .info-laporan td { border: none; padding: 1px 0; vertical-align: top; }
        .info-laporan td.label { width: 110px; color: #0f172a; }
        .info-laporan td.titik { width: 14px; }
        .info-laporan td.nilai { font-weight: 700; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data thead { display: table-header-group; }
        table.data th { background: #f1f5f9; color: #000; text-align: center; padding: 7px 8px; border: 1px solid #000; font-size: 11px; font-weight: 700; }
        table.data td { padding: 5px 8px; border: 1px solid #000; vertical-align: top; }
        table.data tr { page-break-inside: avoid; }
        table.data td.no { width: 32px; text-align: center; }
        table.data td.mono { font-family: Consolas, "Courier New", monospace; white-space: nowrap; font-weight: 700; }
        table.data td.nowrap { white-space: nowrap; }
        .kosong { text-align: center; padding: 24px; color: #64748b; }
        .catatan { margin-top: 10px; font-size: 11px; color: #64748b; }

        .ttd { margin-top: 28px; display: flex; justify-content: flex-end; }
        .ttd .blok { text-align: center; font-size: 12px; width: 260px; }
        .ttd .tanggal { margin-bottom: 4px; }
        .ttd .jabatan { margin-bottom: 64px; }
        .ttd .nama { font-weight: 700; text-decoration: underline; }
        .ttd .nip { margin-top: 2px; }

        @media print {
            body { padding: 0; }
            .bar { display: none; }
        }
    </style>
</head>
<body>
    <div class="bar">
        <button type="button" class="utama" onclick="window.print()">Cetak / Simpan PDF</button>
        <button type="button" onclick="window.close()">Tutup</button>
    </div>

    <div class="kop">
        <img src="{{ asset('images/logo-hst.png') }}" alt="Lambang Kabupaten Hulu Sungai Tengah">
        <div class="teks">
            <div class="instansi">PEMERINTAH KABUPATEN HULU SUNGAI TENGAH</div>
            <div class="dinas">DINAS KESEHATAN</div>
            <div class="satker">UPT RSUD H. DAMANHURI BARABAI</div>
            <div class="alamat">
                Jalan Murakata Nomor 4 Barabai Barat, Hulu Sungai Tengah, Kalimantan Selatan 71314<br>
                Telepon: 08115008080 &middot; Laman: www.rshdbarabai.com &middot; Pos-el: rshd@hstkab.go.id
            </div>
        </div>
    </div>
    <div class="garis-tebal"></div>
    <div class="garis-tipis"></div>

    <h1>{{ $judul }}</h1>
    <div class="subjudul">Sistem Informasi Pengaduan Masyarakat (SI-ADUAN)</div>

    <table class="info-laporan">
        <tr>
            <td class="label">Unit</td><td class="titik">:</td><td class="nilai">{{ $unit }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td><td class="titik">:</td><td class="nilai">{{ $periode }}</td>
        </tr>
        @foreach ($info as $i)
            <tr>
                <td class="label">Keterangan</td><td class="titik">:</td><td class="nilai">{{ $i }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="label">Jumlah Data</td><td class="titik">:</td><td class="nilai">{{ $total }} tiket</td>
        </tr>
        <tr>
            <td class="label">Dicetak</td><td class="titik">:</td>
            <td class="nilai">{{ now()->locale('id')->translatedFormat('j F Y, H.i') }} oleh {{ auth()->user()->name }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                @foreach ($header as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $b)
                <tr>
                    @foreach ($b as $i => $sel)
                        <td class="{{ $i === 0 ? 'no' : ($i === 1 ? 'mono' : ($i >= 4 ? 'nowrap' : '')) }}">{{ $sel }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($header) }}" class="kosong">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($total > $batas)
        <div class="catatan">Hanya {{ $batas }} tiket pertama yang dicetak dari {{ $total }} tiket. Persempit filter atau gunakan unduhan Excel untuk data lengkap.</div>
    @endif

    <div class="ttd">
        <div class="blok">
            <div class="tanggal">Barabai, {{ now()->locale('id')->translatedFormat('j F Y') }}</div>
            <div class="jabatan">Yang mencetak laporan,</div>
            <div class="nama">{{ auth()->user()->name }}</div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 500); });
    </script>
</body>
</html>
