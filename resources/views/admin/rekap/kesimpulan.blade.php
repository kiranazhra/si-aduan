@extends('layouts.admin')

@section('title', 'Kesimpulan')

@section('content')
@php
    $kartu = [
        ['ikon' => 'inbox',        'label' => 'Total Aduan',     'nilai' => $total,                        'by' => 'semua',  'v' => null,              'warna' => 'text-blue-600 bg-blue-50'],
        ['ikon' => 'autorenew',    'label' => 'Diproses',        'nilai' => $perStatus['diproses'],        'by' => 'status', 'v' => 'diproses',        'warna' => 'text-amber-600 bg-amber-50'],
        ['ikon' => 'swap_horiz',   'label' => 'Dikoordinasikan', 'nilai' => $perStatus['dikoordinasikan'], 'by' => 'status', 'v' => 'dikoordinasikan', 'warna' => 'text-blue-600 bg-blue-50'],
        ['ikon' => 'check_circle', 'label' => 'Selesai',         'nilai' => $perStatus['selesai'],         'by' => 'status', 'v' => 'selesai',         'warna' => 'text-emerald-600 bg-emerald-50'],
    ];
@endphp

<div class="space-y-6">
    <div class="no-print">
    <x-admin.judul judul="Kesimpulan" :sub="'Total aduan per status berdasarkan data tahun ' . $tahun">
        <x-admin.filter-periode
                :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId"
                :daftarTahun="$daftarTahun" :daftarUnit="$daftarUnit" />
    </x-admin.judul>
    </div>

    {{-- KPI --}}
    <div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($kartu as $k)
            <button type="button" @click="{{ \App\Support\RekapUi::detail($k['label'], $k['by'], $k['v'], $tahun, $bulan, $tanggal, $unitId) }}"
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4 text-left hover:border-navy hover:shadow-md transition-all group">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0 {{ $k['warna'] }}">
                    <span class="material-icons-outlined text-3xl">{{ $k['ikon'] }}</span>
                </div>
                <div>
                    <div class="text-base text-slate-500">{{ $k['label'] }}</div>
                    <div class="text-4xl font-extrabold text-navy">{{ $k['nilai'] }}</div>
                    <div class="text-sm text-slate-400">tahun {{ $tahun }}</div>
                </div>
                <span class="material-icons-outlined text-slate-200 group-hover:text-emerald-500 transition ml-auto" style="font-size: 22px;">open_in_new</span>
            </button>
        @endforeach
    </div>

    {{-- Tabel lengkap --}}
    <x-admin.kop-cetak judul="Kesimpulan" :tahun="$tahun" :bulan="$bulan" :tanggal="$tanggal" :unitId="$unitId" :daftarUnit="$daftarUnit" />
    <div class="print-title no-print text-xl font-bold text-navy">Kesimpulan — Tahun {{ $tahun }}</div>
    <div class="rekap-tabel bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 no-print">
            <x-admin.export-bar :q="$q" :count="$tiket->total()" :total="$total"
                placeholder="Cari no. tiket, pelapor, atau judul…" />
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-base">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Pelapor', 'Tanggal', 'Kategori', 'Unit', 'Status', 'Grading', 'Waktu Selesai'] as $h)
                            <th class="text-left py-3.5 px-4 text-sm font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiket as $r)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-bold text-sm font-mono whitespace-nowrap">
                                <a href="{{ route('admin.tickets.show', ['aduan' => $r->nomor_tiket]) }}" class="text-navy hover:underline">{{ $r->nomor_tiket }}</a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-sm whitespace-nowrap">{{ $r->anonim ? 'Anonim' : $r->nama_pelapor }}</td>
                            <td class="py-3 px-4 text-slate-500 text-sm whitespace-nowrap">{{ $r->dibuat_pada->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4 text-slate-500 text-sm whitespace-nowrap">{{ $r->kategori->nama ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500 text-sm whitespace-nowrap">{{ $r->unit->nama ?? '—' }}</td>
                            <td class="py-3 px-4"><x-admin.status-badge :status="$r->status" /></td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$r->prioritas" /></td>
                            <td class="py-3 px-4 text-slate-500 text-sm whitespace-nowrap">{{ $r->lamaPenyelesaianHari() !== null ? $r->lamaPenyelesaianHari() . ' hari' : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-10 text-center text-slate-400 text-base">Tidak ada data yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$tiket" />
    </div>

    <div class="no-print text-sm text-slate-400 text-right">Dibuat otomatis oleh SI-ADUAN · {{ now()->locale('id')->translatedFormat('j F Y') }}</div>

    <x-admin.ttd-cetak />

    <x-admin.detail-modal />
</div>
@endsection
