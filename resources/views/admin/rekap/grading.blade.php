@extends('layouts.admin')

@section('title', 'Rekap Grading Unit')

@section('content')
@php
    $kartu = [
        ['label' => 'Baik',   'nilai' => $jumlah['baik'],   'cls' => 'bg-emerald-50 text-emerald-600', 'titik' => 'bg-emerald-500'],
        ['label' => 'Cukup',  'nilai' => $jumlah['cukup'],  'cls' => 'bg-amber-50 text-amber-600',     'titik' => 'bg-amber-400'],
        ['label' => 'Kurang', 'nilai' => $jumlah['kurang'], 'cls' => 'bg-red-50 text-red-600',         'titik' => 'bg-red-500'],
    ];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Rekap Grading Unit" sub="Penilaian kinerja setiap unit pelayanan yang menerima tiket">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    <div class="no-print grid grid-cols-3 gap-4">
        @foreach ($kartu as $k)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 {{ $k['cls'] }}">
                    <span class="w-3.5 h-3.5 rounded-full {{ $k['titik'] }}"></span>
                </div>
                <div class="text-3xl font-extrabold text-navy">{{ $k['nilai'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $k['label'] }}</div>
            </div>
        @endforeach
    </div>

    <x-admin.kop-cetak judul="Rekap Grading Unit" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-base font-bold text-navy">Rekap Grading Unit — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$baris->count()" :total="$semua->count()" placeholder="Cari nama unit…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['Unit', 'Total Aduan', 'Selesai', 'Belum Selesai', 'Waktu Rata-rata', 'Grade'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                        <th class="no-print text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $u)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-semibold text-navy">{{ $u->nama }}</td>
                            <td class="py-3 px-4 font-bold tabular-nums text-navy">{{ $u->total }}</td>
                            <td class="py-3 px-4 text-emerald-600 font-semibold tabular-nums">{{ $u->selesai }}</td>
                            <td class="py-3 px-4 text-red-500 tabular-nums">{{ $u->total - $u->selesai }}</td>
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ $u->rata_hari === null ? '—' : $u->rata_hari . ' hari' }}</td>
                            <td class="py-3 px-4"><span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $u->grade['cls'] }}">{{ $u->grade['label'] }}</span></td>
                            <td class="no-print py-3 px-4">
                                <button type="button" @click="{{ \App\Support\RekapUi::detail($u->nama, 'unit', $u->unit_id, $tahun, $bulan, $tanggal, $unitId) }}"
                                        class="text-xs text-emerald-700 border border-emerald-200 rounded-lg px-2.5 py-1 hover:bg-emerald-50 transition font-semibold whitespace-nowrap flex items-center gap-1">
                                    <span class="material-icons-outlined" style="font-size: 13px;">open_in_new</span>Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                            <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 36px;">search_off</span>
                            Belum ada tiket yang diteruskan ke unit pada tahun {{ $tahun }}.
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
