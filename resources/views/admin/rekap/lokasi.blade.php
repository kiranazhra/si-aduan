@extends('layouts.admin')

@section('title', 'Rekap Lokasi / Ruangan')

@section('content')
@php
    $maks = max(1, (int) $baris->max('total'));
    $kartu = [
        ['label' => 'Total Lokasi',     'nilai' => $jumlahLokasi,    'ikon' => 'place',       'warna' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Total Aduan',      'nilai' => $totalAduan,      'ikon' => 'inbox',       'warna' => 'bg-slate-50 text-slate-600'],
        ['label' => 'Lokasi Tertinggi', 'nilai' => $jumlahTertinggi, 'ikon' => 'trending_up', 'warna' => 'bg-red-50 text-red-600'],
        ['label' => 'Belum Selesai',    'nilai' => $belumSelesai,    'ikon' => 'schedule',    'warna' => 'bg-amber-50 text-amber-600'],
    ];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Rekap Lokasi / Ruangan" sub="Distribusi pengaduan berdasarkan lokasi kejadian">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    <div class="no-print grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach ($kartu as $k)
            <x-admin.stat-card :ikon="$k['ikon']" :warna="$k['warna']" :nilai="$k['nilai']" :label="$k['label']" />
        @endforeach
    </div>

    <x-admin.kop-cetak judul="Rekap Lokasi / Ruangan" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-base font-bold text-navy">Rekap Lokasi / Ruangan — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$baris->count()" :total="$jumlahLokasi" placeholder="Cari lokasi atau ruangan…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['Lokasi / Ruangan', 'Total', 'Diproses', 'Dikoordinasikan', 'Selesai'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                        <th class="no-print text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baris as $r)
                        @php $tertinggiIni = $namaTertinggi !== null && $r->nama === $namaTertinggi; @endphp
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition {{ $tertinggiIni ? 'bg-red-50/40' : '' }}">
                            <td class="py-3 px-4 font-semibold text-navy">
                                <div class="flex items-center gap-2">
                                    @if ($tertinggiIni)
                                        <span class="material-icons-outlined text-red-400" style="font-size: 16px;">warning</span>
                                    @endif
                                    {{ $r->nama }}
                                </div>
                            </td>
                            <td class="py-3 px-4 font-bold tabular-nums text-navy">{{ $r->total }}</td>
                            <td class="py-3 px-4 text-amber-600 tabular-nums font-semibold">{{ $r->diproses }}</td>
                            <td class="py-3 px-4 text-blue-600 tabular-nums font-semibold">{{ $r->dikoordinasikan }}</td>
                            <td class="py-3 px-4 text-emerald-600 tabular-nums font-semibold">{{ $r->selesai }}</td>
                            <td class="no-print py-3 px-4">
                                <button type="button" @click="{{ \App\Support\RekapUi::detail($r->nama, 'lokasi', $r->id, $tahun, $bulan, $tanggal, $unitId) }}"
                                        class="text-xs text-emerald-700 border border-emerald-200 rounded-lg px-2.5 py-1 hover:bg-emerald-50 transition font-semibold flex items-center gap-1 whitespace-nowrap">
                                    <span class="material-icons-outlined" style="font-size: 15px;">open_in_new</span>Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-slate-400 text-sm">Tidak ada lokasi yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
