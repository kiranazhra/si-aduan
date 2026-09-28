@extends('layouts.admin')

@section('title', 'Rekap Data Aduan')

@section('content')
@php
    $bulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
    $puncak = max(1, max($perBulan));

    $kartu = [
        ['label' => 'Total Aduan',     'nilai' => $total,                       'by' => 'semua',  'v' => null,              'ikon' => 'inbox',        'warna' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Selesai',         'nilai' => $perStatus['selesai'],         'by' => 'status', 'v' => 'selesai',         'ikon' => 'check_circle', 'warna' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Dalam Proses',    'nilai' => $perStatus['diproses'],        'by' => 'status', 'v' => 'diproses',        'ikon' => 'autorenew',    'warna' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Dikoordinasikan', 'nilai' => $perStatus['dikoordinasikan'], 'by' => 'status', 'v' => 'dikoordinasikan', 'ikon' => 'swap_horiz',   'warna' => 'bg-blue-50 text-blue-600'],
    ];

    $irisan = [
        ['name' => 'Diproses',        'key' => 'diproses',        'color' => '#f59e0b', 'value' => $perStatus['diproses']],
        ['name' => 'Dikoordinasikan', 'key' => 'dikoordinasikan', 'color' => '#3b82f6', 'value' => $perStatus['dikoordinasikan']],
        ['name' => 'Selesai',         'key' => 'selesai',         'color' => '#059669', 'value' => $perStatus['selesai']],
    ];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Rekap Data Aduan" :sub="'Ringkasan statistik pengaduan tahun ' . $tahun">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    {{-- Kartu (bisa diklik) --}}
    <div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($kartu as $k)
            <button type="button" @click="{{ \App\Support\RekapUi::detail($k['label'], $k['by'], $k['v'], $tahun, $bulan, $tanggal, $unitId) }}"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-left hover:border-navy hover:shadow-md transition-all group">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 {{ $k['warna'] }}">
                    <span class="material-icons-outlined" style="font-size: 18px;">{{ $k['ikon'] }}</span>
                </div>
                <div class="text-3xl font-extrabold text-navy">{{ $k['nilai'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $k['label'] }}</div>
                <div class="text-[10px] text-emerald-600 mt-1 opacity-0 group-hover:opacity-100 transition flex items-center gap-0.5">
                    <span class="material-icons-outlined" style="font-size: 11px;">open_in_new</span>Lihat detail
                </div>
            </button>
        @endforeach
    </div>

    {{-- Grafik --}}
    <div class="no-print grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="text-sm font-bold mb-4 text-navy">Aduan per Bulan</div>
            <div class="flex gap-1.5 h-40">
                @foreach ($perBulan as $i => $v)
                    <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                        <div class="text-[9px] text-slate-400">{{ $v }}</div>
                        <div class="w-full flex-1 flex items-end">
                            <div class="w-full rounded-t-md"
                                 :style="'height: {{ round($v / $puncak * 100) }}%; min-height: 2px; background: linear-gradient(180deg,#1a4a8a,#0f2e5a);'"></div>
                        </div>
                        <div class="text-[9px] text-slate-400">{{ $bulanSingkat[$i] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="text-sm font-bold mb-4 text-navy">Distribusi Status</div>
            <div class="flex items-center gap-4">
                <x-admin.donut :items="$irisan" :ukuran="130" :tebal="24" />
                <div class="space-y-2 flex-1">
                    @foreach ($irisan as $d)
                        <button type="button" @click="{{ \App\Support\RekapUi::detail($d['name'], 'status', $d['key'], $tahun, $bulan, $tanggal, $unitId) }}"
                                class="flex items-center gap-2 text-xs hover:opacity-70 transition w-full text-left">
                            <div class="w-2.5 h-2.5 rounded-full shrink-0" :style="'background-color: {{ $d['color'] }};'"></div>
                            <span class="text-slate-600">{{ $d['name'] }}</span>
                            <span class="font-bold ml-auto text-navy">{{ $d['value'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <x-admin.kop-cetak judul="Rekap Data Aduan" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-base font-bold text-navy">Rekap Data Aduan — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$tiket->total()" :total="$jumlahSemua"
                placeholder="Cari no. tiket, pelapor, atau judul…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Pelapor', 'Tanggal', 'Ringkasan', 'Kategori', 'Grading', 'Status'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiket as $r)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-xs font-mono whitespace-nowrap">
                                <a href="{{ route('admin.tickets.show', ['aduan' => $r->nomor_tiket]) }}" class="text-navy hover:underline">{{ $r->nomor_tiket }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">{{ $r->anonim ? 'Anonim' : $r->nama_pelapor }}</td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->dibuat_pada->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs max-w-[220px]"><span class="line-clamp-2 leading-snug">{{ $r->judul }}</span></td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->kategori->nama ?? '-' }}</td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$r->prioritas" /></td>
                            <td class="py-3 px-4"><x-admin.status-badge :status="$r->status" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                            <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 36px;">search_off</span>Tidak ada data yang sesuai.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$tiket" />
    </div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
