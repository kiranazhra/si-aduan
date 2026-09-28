@extends('layouts.admin')

@section('title', 'Rekap Rating Pelayanan')

@section('content')
@php
    $warnaBar = [5 => 'bg-emerald-500', 4 => 'bg-lime-500', 3 => 'bg-amber-400', 2 => 'bg-orange-500', 1 => 'bg-red-500'];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Rekap Rating Pelayanan" sub="Penilaian bintang dari pelapor terhadap pelayanan rumah sakit">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    {{-- Ringkasan --}}
    <div class="no-print grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="text-xs text-slate-500 mb-1">Rata-rata Rating</div>
            <div class="flex items-end gap-2">
                <div class="text-4xl font-extrabold text-navy tabular-nums">{{ $rata === null ? '—' : number_format($rata, 1, ',', '') }}</div>
                <div class="text-sm text-slate-400 mb-1">/ 5</div>
            </div>
            <div class="mt-2"><x-admin.bintang :nilai="$rata ?? 0" ukuran="w-5 h-5" /></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 bg-blue-50 text-blue-600">
                <span class="material-icons-outlined" style="font-size: 18px;">rate_review</span>
            </div>
            <div class="text-3xl font-extrabold text-navy">{{ $totalUlasan }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Total Ulasan <span class="text-slate-400">(dari {{ $totalAduan }} aduan)</span></div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 bg-emerald-50 text-emerald-600">
                <span class="material-icons-outlined" style="font-size: 18px;">thumb_up</span>
            </div>
            <div class="text-3xl font-extrabold text-navy">{{ $persenPuas }}%</div>
            <div class="text-xs text-slate-500 mt-0.5">Puas &amp; Sangat Puas</div>
        </div>
    </div>

    {{-- Sebaran bintang --}}
    <div class="no-print bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="text-sm font-bold text-navy mb-4">Sebaran Penilaian</div>
        <div class="space-y-2.5">
            @foreach ($sebaran as $s)
                <button type="button"
                        @click="{{ \App\Support\RekapUi::detail($s->label . ' (' . $s->nilai . ' bintang)', 'rating', $s->nilai, $tahun, $bulan, $tanggal, $unitId) }}"
                        class="w-full flex items-center gap-3 text-left group">
                    <div class="w-32 shrink-0 flex items-center gap-1.5">
                        <span class="text-sm font-bold text-navy tabular-nums w-3">{{ $s->nilai }}</span>
                        <svg viewBox="0 0 24 24" class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <span class="text-xs text-slate-500 truncate">{{ $s->label }}</span>
                    </div>
                    <div class="flex-1 h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $warnaBar[$s->nilai] }}" style="width: {{ $s->pct }}%;"></div>
                    </div>
                    <div class="w-20 shrink-0 text-right text-xs tabular-nums text-slate-600 group-hover:text-navy">
                        <span class="font-bold">{{ $s->jumlah }}</span> <span class="text-slate-400">({{ $s->pct }}%)</span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Tabel per unit --}}
    <x-admin.kop-cetak judul="Rekap Rating Pelayanan per Unit" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-base font-bold text-navy">Rekap Rating Pelayanan per Unit — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$baris->count()" :total="$semua->count()" placeholder="Cari nama unit…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['Unit', 'Ulasan', 'Rata-rata', 'Sangat Puas (5)', 'Puas (4)', 'Cukup Puas (3)', 'Kurang Puas (2)', 'Tidak Puas (1)'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                        <th class="no-print text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $r)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-navy">{{ $r->nama }}</td>
                            <td class="py-3 px-4 font-bold tabular-nums text-navy">{{ $r->total }}</td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <x-admin.bintang :nilai="$r->rata" ukuran="w-3.5 h-3.5" />
                                    <span class="text-xs font-bold text-slate-600 tabular-nums">{{ number_format($r->rata, 1, ',', '') }}</span>
                                </div>
                            </td>
                            @foreach ([5, 4, 3, 2, 1] as $n)
                                <td class="py-3 px-4 tabular-nums text-slate-600">{{ $r->per[$n] }}</td>
                            @endforeach
                            <td class="no-print py-3 px-4">
                                <button type="button" @click="{{ \App\Support\RekapUi::detail($r->nama, 'ratingunit', $r->unit_id, $tahun, $bulan, $tanggal, null) }}"
                                        class="text-xs text-emerald-700 border border-emerald-200 rounded-lg px-2.5 py-1 hover:bg-emerald-50 transition font-semibold whitespace-nowrap flex items-center gap-1">
                                    <span class="material-icons-outlined" style="font-size: 13px;">open_in_new</span>Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="py-10 text-center text-slate-400 text-sm">
                            <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 36px;">star_border</span>
                            Belum ada penilaian pada periode ini.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
