@extends('layouts.admin')

@section('title', 'Berkas Rekap')

@section('content')
<div class="space-y-6">
    <x-admin.judul judul="Berkas Rekap" sub="Unduh laporan rekap langsung dari data terbaru">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
        <form method="GET" class="flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2.5">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <span class="material-icons-outlined text-slate-400" style="font-size: 20px;">search</span>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari berkas…"
                   class="flex-1 text-sm focus:outline-none text-slate-700 placeholder:text-slate-400">
        </form>

        <div class="space-y-2">
            @forelse ($daftar as $d)
                <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3.5 border border-slate-100 rounded-xl hover:border-slate-200 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shrink-0 bg-emerald-600">
                            <span class="material-icons-outlined" style="font-size: 20px;">table_view</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-navy">{{ $d['nama'] }} {{ $tahun }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $d['ket'] }} · dibuat saat diunduh</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($d['rute'], ['tahun' => $tahun, 'export' => 'csv']) }}"
                           class="flex items-center gap-1.5 text-xs text-emerald-700 border border-emerald-200 rounded-lg px-3 py-1.5 hover:bg-emerald-50 transition font-semibold">
                            <span class="material-icons-outlined" style="font-size: 16px;">download</span>Excel
                        </a>
                        <a href="{{ route($d['rute'], ['tahun' => $tahun, 'cetak' => 1]) }}" target="_blank" rel="noopener"
                           class="flex items-center gap-1.5 text-xs text-red-600 border border-red-200 rounded-lg px-3 py-1.5 hover:bg-red-50 transition font-semibold">
                            <span class="material-icons-outlined" style="font-size: 16px;">picture_as_pdf</span>PDF
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-slate-400 text-sm">
                    <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 38px;">search_off</span>
                    Berkas tidak ditemukan.
                </div>
            @endforelse
        </div>

        <div class="text-xs text-slate-400 leading-relaxed">
            Tombol <strong>Excel</strong> mengunduh file CSV yang bisa dibuka di Excel. Tombol <strong>PDF</strong> membuka halaman
            laporan lalu menampilkan jendela cetak; pilih “Simpan sebagai PDF” pada printer.
        </div>
    </div>
</div>
@endsection
