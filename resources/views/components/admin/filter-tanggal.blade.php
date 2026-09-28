{{-- Tiga pilihan filter: Tanggal, Bulan, Tahun. Pasang di dalam <form method="GET">.
     Tanggal (harian) paling kuat: bila diisi, Bulan dan Tahun diabaikan. --}}
@props(['tanggal' => null, 'bulan' => null, 'tahun' => null, 'daftarTahun' => []])

<input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
       title="Filter per hari (mengabaikan pilihan Bulan dan Tahun)"
       class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">

<select name="bulan" onchange="this.form.submit()"
        class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">
    <option value="">Semua Bulan</option>
    @foreach (\App\Support\PeriodeFilter::BULAN as $angka => $nama)
        <option value="{{ $angka }}" @selected($angka == $bulan)>{{ $nama }}</option>
    @endforeach
</select>

<select name="tahun" onchange="this.form.submit()"
        class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">
    <option value="">Semua Tahun</option>
    @foreach ($daftarTahun as $t)
        <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
    @endforeach
</select>
