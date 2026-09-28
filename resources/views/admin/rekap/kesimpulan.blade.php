@extends('layouts.admin')

@section('title', 'Kesimpulan & Rekomendasi')

@section('content')
@php
    $kartu = [
        ['ikon' => 'trending_up',  'label' => 'Total Aduan',        'nilai' => (string) $total,          'sub' => 'tahun ' . $tahun,      'warna' => 'text-amber-600 bg-amber-50',     'by' => 'semua',  'v' => null],
        ['ikon' => 'check_circle', 'label' => 'Selesai',            'nilai' => (string) $selesai,         'sub' => 'tiket sudah selesai',  'warna' => 'text-emerald-600 bg-emerald-50', 'by' => 'status', 'v' => 'selesai'],
        ['ikon' => 'schedule',     'label' => 'Belum Diselesaikan', 'nilai' => (string) $belum,           'sub' => 'tiket aktif',          'warna' => 'text-red-600 bg-red-50',         'by' => 'belum',  'v' => null],
    ];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Kesimpulan & Rekomendasi" :sub="'Analisis akhir dan tindak lanjut berbasis data aduan ' . $tahun">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    {{-- KPI --}}
    <div class="no-print grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ($kartu as $k)
            <button type="button" @click="{{ \App\Support\RekapUi::detail($k['label'], $k['by'], $k['v'], $tahun, $bulan, $tanggal, $unitId) }}"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4 text-left hover:border-navy hover:shadow-md transition-all group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $k['warna'] }}">
                    <span class="material-icons-outlined text-xl">{{ $k['ikon'] }}</span>
                </div>
                <div>
                    <div class="text-xs text-slate-500">{{ $k['label'] }}</div>
                    <div class="text-xl font-extrabold text-navy">{{ $k['nilai'] }}</div>
                    <div class="text-xs text-slate-400">{{ $k['sub'] }}</div>
                </div>
                <span class="material-icons-outlined text-slate-200 group-hover:text-emerald-500 transition ml-auto" style="font-size: 16px;">open_in_new</span>
            </button>
        @endforeach
    </div>

    {{-- Rekomendasi (dibuat otomatis dari data) --}}
    <div class="no-print bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="text-sm font-bold text-navy">Rekomendasi Tindak Lanjut</div>
        @foreach ($rekomendasi as $r)
            <div class="flex gap-4 pb-4 border-b border-slate-50 last:border-0 last:pb-0">
                <div class="w-7 h-7 rounded-full grad-btn flex items-center justify-center shrink-0 text-white text-xs font-bold">{{ $loop->iteration }}</div>
                <div>
                    <div class="text-sm font-semibold mb-1 text-navy">{{ $r['head'] }}</div>
                    <div class="text-sm text-slate-500 leading-relaxed">{{ $r['body'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabel lengkap --}}
    <x-admin.kop-cetak judul="Kesimpulan & Rekomendasi" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-base font-bold text-navy">Kesimpulan & Rekomendasi — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$tiket->total()" :total="$total"
                placeholder="Cari no. tiket, pelapor, atau judul…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Pelapor', 'Tanggal', 'Kategori', 'Unit', 'Status', 'Grading', 'Waktu Selesai'] as $h)
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
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->kategori->nama ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->unit->nama ?? '—' }}</td>
                            <td class="py-3 px-4"><x-admin.status-badge :status="$r->status" /></td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$r->prioritas" /></td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->lamaPenyelesaianHari() !== null ? $r->lamaPenyelesaianHari() . ' hari' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-slate-400 text-sm">Tidak ada data yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$tiket" />
    </div>

    <div class="no-print text-xs text-slate-400 text-right">Dibuat otomatis oleh SI-ADUAN · {{ now()->locale('id')->translatedFormat('j F Y') }}</div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
