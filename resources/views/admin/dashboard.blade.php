@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $kartu = [
        ['label' => 'Total Pengaduan', 'nilai' => $total,           'ikon' => 'inbox',        'warna' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Selesai',         'nilai' => $selesai,         'ikon' => 'check_circle', 'warna' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Diproses',        'nilai' => $diproses,        'ikon' => 'autorenew',    'warna' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Dikoordinasikan', 'nilai' => $dikoordinasikan, 'ikon' => 'swap_horiz',   'warna' => 'bg-blue-50 text-blue-600'],
    ];

    $irisan = [
        ['name' => 'Selesai',         'value' => $selesai,         'color' => '#059669'],
        ['name' => 'Diproses',        'value' => $diproses,        'color' => '#f59e0b'],
        ['name' => 'Dikoordinasikan', 'value' => $dikoordinasikan, 'color' => '#3b82f6'],
    ];
@endphp

<div class="space-y-6">
    <x-admin.judul judul="Dashboard"
        :sub="'Ringkasan pengaduan masuk — ' . now()->locale('id')->translatedFormat('l, j F Y')" />

    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($kartu as $k)
            <x-admin.stat-card :ikon="$k['ikon']" :warna="$k['warna']" :nilai="$k['nilai']" :label="$k['label']" />
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Tingkat penyelesaian --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="text-sm font-bold mb-4 text-navy">Tingkat Penyelesaian</div>
            <div class="flex justify-center">
                <x-admin.donut :items="$irisan" :ukuran="160" :tebal="26" />
            </div>
            <div class="space-y-1.5 mt-4">
                @foreach ($irisan as $d)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $d['color'] }};"></div>
                            <span class="text-slate-600">{{ $d['name'] }}</span>
                        </div>
                        <span class="font-semibold text-navy">{{ $d['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tiket terbaru --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm font-bold text-navy">Tiket Terbaru</div>
                <a href="{{ route('admin.tickets') }}" class="text-xs text-emerald-700 font-semibold hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            @foreach (['No. Tiket', 'Pelapor', 'Kategori', 'Status', ''] as $h)
                                <th class="text-left py-2 px-2 text-xs font-semibold text-slate-400">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($terbaru as $t)
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                <td class="py-2.5 px-2 font-semibold text-xs font-mono text-navy whitespace-nowrap">{{ $t->nomor_tiket }}</td>
                                <td class="py-2.5 px-2 text-slate-600 text-xs">{{ $t->anonim ? 'Anonim' : $t->nama_pelapor }}</td>
                                <td class="py-2.5 px-2 text-slate-600 text-xs">{{ $t->kategori->nama ?? '-' }}</td>
                                <td class="py-2.5 px-2"><x-admin.status-badge :status="$t->status" /></td>
                                <td class="py-2.5 px-2">
                                    <a href="{{ route('admin.tickets.show', ['aduan' => $t->nomor_tiket]) }}" class="text-xs text-emerald-700 hover:underline font-semibold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-slate-400 text-sm">Belum ada pengaduan masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
