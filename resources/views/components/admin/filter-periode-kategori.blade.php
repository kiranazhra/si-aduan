{{-- Filter untuk halaman Rekap Kategori: Tahun, Bulan, Tanggal (harian), Kategori.
     Beda dari x-admin.filter-periode biasa - dropdown-nya Kategori, bukan Unit,
     karena halaman ini dikelompokkan per kategori.
     Tanggal punya prioritas tertinggi - kalau diisi, Bulan diabaikan otomatis di backend. --}}
@props(['tahun', 'bulan' => null, 'tanggal' => null, 'kategoriId' => null, 'daftarTahun', 'daftarKategori'])

@php
    $bulanNama = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                  7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
@endphp

<form method="GET" class="no-print flex flex-wrap items-center gap-2">
    @foreach (request()->except(['tahun', 'bulan', 'tanggal', 'kategori_id', 'page', 'export', 'cetak']) as $k => $v)
        @if (is_scalar($v))
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    {{-- Tanggal harian - kalau diisi, prioritas tertinggi --}}
    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
           class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition"
           title="Filter per hari (mengabaikan pilihan Bulan)">

    {{-- Bulan --}}
    <select name="bulan" onchange="this.form.submit()"
            class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">
        <option value="">Semua Bulan</option>
        @foreach ($bulanNama as $angka => $nama)
            <option value="{{ $angka }}" @selected($angka == $bulan)>{{ $nama }}</option>
        @endforeach
    </select>

    {{-- Tahun --}}
    <select name="tahun" onchange="this.form.submit()"
            class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">
        @foreach ($daftarTahun as $t)
            <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
        @endforeach
    </select>

    {{-- Kategori --}}
    <select name="kategori_id" onchange="this.form.submit()"
            class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition max-w-[180px]">
        <option value="">Semua Kategori</option>
        @foreach ($daftarKategori as $k)
            <option value="{{ $k->id }}" @selected($k->id == $kategoriId)>{{ $k->nama }}</option>
        @endforeach
    </select>

    @if ($tanggal || $bulan || $kategoriId)
        <a href="{{ url()->current() }}?tahun={{ $tahun }}"
           class="text-xs text-slate-400 hover:text-red-500 flex items-center gap-1 px-2">
            <span class="material-icons-outlined" style="font-size: 16px;">close</span>Reset
        </a>
    @endif
</form>
