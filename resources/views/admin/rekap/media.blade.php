@extends('layouts.admin')

@section('title', 'Rekap Media / Sarana')

@section('content')
@php
    $irisan = $semua->map(fn ($r) => ['name' => $r->nama, 'value' => $r->total, 'color' => $r->warna])->all();
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Rekap Media / Sarana" sub="Distribusi pengaduan berdasarkan saluran yang digunakan">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    {{-- Kartu per saluran --}}
    <div class="no-print grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach ($semua as $r)
            <button type="button" @click="{{ \App\Support\RekapUi::detail($r->nama, 'media', $r->kode, $tahun, $bulan, $tanggal, $unitId) }}"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-left hover:border-navy hover:shadow-md transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background-color: {{ $r->warna }}20; color: {{ $r->warna }};">
                    <span class="material-icons-outlined" style="font-size: 20px;">{{ $r->ikon }}</span>
                </div>
                <div class="text-3xl font-extrabold text-navy">{{ $r->total }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $r->nama }}</div>
                <div class="text-[12px] mt-1 opacity-0 group-hover:opacity-100 transition flex items-center gap-0.5" style="color: {{ $r->warna }};">
                    <span class="material-icons-outlined" style="font-size: 13px;">open_in_new</span>Lihat detail
                </div>
            </button>
        @endforeach
    </div>

    <div>

        {{-- Tabel --}}
        <div class="w-full">
        <x-admin.kop-cetak judul="Rekap Media / Sarana" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
        <div class="print-title no-print text-base font-bold text-navy">Rekap Media / Sarana — Tahun {{ $tahun }}</div>
        <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 no-print">
                <x-admin.export-bar :q="$q" :count="$baris->count()" :total="$semua->count()" placeholder="Cari saluran…" />
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            @foreach (['Saluran', 'Total', 'Selesai'] as $h)
                                <th class="text-left py-3 px-4 text-xs font-bold text-slate-400">{{ $h }}</th>
                            @endforeach
                            <th class="no-print text-left py-3 px-4 text-xs font-bold text-slate-400"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($baris as $r)
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                <td class="py-3 px-4 font-semibold text-navy">{{ $r->nama }}</td>
                                <td class="py-3 px-4 font-bold tabular-nums text-navy">{{ $r->total }}</td>
                                <td class="py-3 px-4 text-emerald-600 tabular-nums font-semibold">{{ $r->selesai }}</td>
                                <td class="no-print py-3 px-4">
                                    <button type="button" @click="{{ \App\Support\RekapUi::detail($r->nama, 'media', $r->kode, $tahun, $bulan, $tanggal, $unitId) }}"
                                            class="text-xs text-emerald-700 border border-emerald-200 rounded-lg px-2.5 py-1 hover:bg-emerald-50 transition font-semibold flex items-center gap-1">
                                        <span class="material-icons-outlined" style="font-size: 15px;">open_in_new</span>Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-10 text-center text-slate-400 text-sm">Tidak ada saluran yang sesuai.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
