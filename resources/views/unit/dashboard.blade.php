@extends('layouts.admin')

@section('title', 'Dashboard Unit')

@section('content')
@php
    $pengguna = auth()->user();
    $kartu = [
        ['label' => 'Tiket Masuk ke Unit', 'nilai' => $total,           'ikon' => 'inbox',        'warna' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Sedang Ditangani',    'nilai' => $diproses,        'ikon' => 'autorenew',    'warna' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Dikoordinasikan',     'nilai' => $dikoordinasikan, 'ikon' => 'swap_horiz',   'warna' => 'bg-blue-50 text-blue-600'],
        ['label' => 'Selesai',             'nilai' => $selesai,         'ikon' => 'check_circle', 'warna' => 'bg-emerald-50 text-emerald-600'],
    ];
@endphp

<div class="space-y-6">

    {{-- Header unit --}}
    <div class="flex items-start justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-7 h-7 rounded-lg grad-btn flex items-center justify-center">
                    <span class="material-icons-outlined text-white" style="font-size: 15px;">apartment</span>
                </div>
                <span class="text-xs font-semibold text-slate-500">{{ $pengguna->unit->nama ?? 'Unit belum ditetapkan' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-navy">Dashboard Unit</h1>
            <p class="text-sm text-slate-500 mt-0.5">Selamat datang, <span class="font-semibold">{{ $pengguna->name }}</span></p>
        </div>
        <div class="text-xs text-slate-400 text-right mt-1">{{ now()->locale('id')->translatedFormat('l, j F Y') }}</div>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($kartu as $k)
            <x-admin.stat-card :ikon="$k['ikon']" :warna="$k['warna']" :nilai="$k['nilai']" :label="$k['label']" />
        @endforeach
    </div>

    {{-- Penilaian pelayanan dari pelapor --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="text-sm font-bold text-navy mb-4">Penilaian Pelayanan Unit Ini</div>
        <div class="grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-6 items-center">
            <div>
                <div class="flex items-end gap-2">
                    <div class="text-4xl font-extrabold text-navy tabular-nums">{{ $rataRating === null ? '—' : number_format($rataRating, 1, ',', '') }}</div>
                    <div class="text-sm text-slate-400 mb-1">/ 5</div>
                </div>
                <div class="mt-1.5"><x-admin.bintang :nilai="$rataRating ?? 0" ukuran="w-5 h-5" /></div>
                <div class="text-xs text-slate-400 mt-1.5">{{ $jumlahRating }} ulasan dari pelapor</div>
            </div>
            <div class="space-y-1.5">
                @php $warnaBar = [5 => 'bg-emerald-500', 4 => 'bg-lime-500', 3 => 'bg-amber-400', 2 => 'bg-orange-500', 1 => 'bg-red-500']; @endphp
                @foreach ($sebaranRating as $s)
                    <div class="flex items-center gap-3 text-xs">
                        <div class="w-28 shrink-0 text-slate-500"><span class="font-bold text-navy">{{ $s->nilai }}</span> · {{ $s->label }}</div>
                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $warnaBar[$s->nilai] }}" style="width: {{ $s->pct }}%;"></div>
                        </div>
                        <div class="w-8 text-right tabular-nums text-slate-600 font-semibold">{{ $s->jumlah }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Info hak akses --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-3.5 flex items-start gap-3 text-sm text-blue-700">
        <span class="material-icons-outlined shrink-0 mt-0.5" style="font-size: 16px;">shield</span>
        <span>
            Anda masuk sebagai <span class="font-bold">Petugas Unit — {{ $pengguna->unit->nama ?? '-' }}</span>.
            Hanya tiket yang didisposisikan ke unit ini yang ditampilkan.
        </span>
    </div>

    {{-- Tiket terbaru --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="text-sm font-bold text-navy">Tiket Aktif untuk Unit Ini</div>
            <a href="{{ route('unit.tickets') }}" class="text-xs text-emerald-700 font-semibold hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Ringkasan Aduan', 'Grading', 'Tanggal', 'Status', 'Aksi'] as $h)
                            <th class="text-left py-2.5 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($terbaru as $t)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-xs font-mono text-navy whitespace-nowrap">{{ $t->nomor_tiket }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs max-w-[240px]">
                                <span class="line-clamp-2 leading-snug">{{ $t->judul }}</span>
                            </td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$t->prioritas" /></td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $t->dibuat_pada?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4"><x-admin.status-badge :status="$t->status" /></td>
                            <td class="py-3 px-4">
                                <a href="{{ route('unit.tickets.show', ['aduan' => $t->nomor_tiket]) }}"
                                   class="inline-flex items-center gap-1 text-xs text-white grad-btn font-semibold px-3 py-1.5 rounded-lg hover:opacity-90 transition whitespace-nowrap">
                                    <span class="material-icons-outlined" style="font-size: 13px;">reply</span>
                                    Tanggapi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 36px;">inbox</span>
                                Tidak ada tiket aktif untuk unit ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
