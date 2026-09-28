{{-- Kop surat resmi, hanya tampil saat dicetak/disimpan sebagai PDF (lihat .kop-cetak di layouts.admin) --}}
@props(['judul', 'tahun' => null, 'bulan' => null, 'tanggal' => null, 'unitId' => null, 'daftarUnit' => null])
@php
    $periode = 'Tahun ' . $tahun;
    if (!empty($tanggal)) {
        $periode = \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y');
    } elseif (!empty($bulan)) {
        $periode = \Carbon\Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->locale('id')->translatedFormat('F Y');
    }

    $namaUnit = 'Semua Unit';
    if (!empty($unitId) && $daftarUnit) {
        $u = collect($daftarUnit)->firstWhere('id', (int) $unitId);
        $namaUnit = $u->nama ?? $namaUnit;
    }
@endphp
<div class="kop-cetak">
    <div style="display:flex; align-items:center; gap:16px;">
        <img src="{{ asset('images/logo-hst.png') }}" alt="Lambang Kabupaten Hulu Sungai Tengah" style="height:70px; width:auto; flex-shrink:0;">
        <div style="flex:1; text-align:center; color:#000;">
            <div style="font-size:13px; font-weight:700; letter-spacing:.2px;">PEMERINTAH KABUPATEN HULU SUNGAI TENGAH</div>
            <div style="font-size:15px; font-weight:800; letter-spacing:.2px;">DINAS KESEHATAN</div>
            <div style="font-size:16px; font-weight:800; letter-spacing:.3px;">UPT RSUD H. DAMANHURI BARABAI</div>
            <div style="font-size:10px; margin-top:3px; color:#1f2937;">
                Jalan Murakata Nomor 4 Barabai Barat, Hulu Sungai Tengah, Kalimantan Selatan 71314<br>
                Telepon: 08115008080 &middot; Laman: www.rshdbarabai.com &middot; Pos-el: rshd@hstkab.go.id
            </div>
        </div>
    </div>
    <div style="border-bottom:3px solid #000; margin-top:8px;"></div>
    <div style="border-bottom:1px solid #000; margin-bottom:10px;"></div>

    <h1 style="font-size:14px; margin:2px 0; text-align:center; text-transform:uppercase; text-decoration:underline; font-weight:700; color:#000;">
        {{ $judul }}
    </h1>
    <div style="text-align:center; font-size:11px; margin-bottom:10px; color:#000;">
        Periode: <strong>{{ $periode }}</strong>
        &middot; Unit: <strong>{{ $namaUnit }}</strong>
        &middot; Dicetak: {{ now()->locale('id')->translatedFormat('j F Y, H.i') }} oleh {{ auth()->user()->name }}
    </div>
</div>
