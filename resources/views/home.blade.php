@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
@php
    $steps = [
        ['icon' => 'edit_note', 'title' => 'Isi Formulir', 'desc' => 'Lengkapi data diri dan uraian pengaduan Anda.'],
        ['icon' => 'confirmation_number', 'title' => 'Terima Nomor Tiket', 'desc' => 'Simpan nomor tiket untuk memantau perkembangan.'],
        ['icon' => 'track_changes', 'title' => 'Pantau Status', 'desc' => 'Cek perkembangan penanganan kapan saja.'],
    ];

    $stats = [
        ['label' => 'Pengaduan Masuk', 'value' => number_format($totalAduan ?? 1240, 0, ',', '.'), 'icon' => 'inbox'],
        ['label' => 'Sudah Ditangani', 'value' => number_format($totalSelesai ?? 1187, 0, ',', '.'), 'icon' => 'check_circle'],
        ['label' => 'Rata-rata Respons', 'value' => ($rataRespons ?? 2) . ' Hari', 'icon' => 'schedule'],
    ];
@endphp

<div class="max-w-4xl mx-auto px-4 py-16 space-y-16">

    {{-- Hero --}}
    <div class="text-center space-y-6">
        <div class="inline-flex items-center gap-2 bg-white/90 text-emerald-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200 shadow-sm">
            <span class="material-icons-outlined text-sm">verified</span>
            Layanan Resmi RSUD H. Damanhuri Barabai
        </div>

        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight text-navy [text-shadow:0_2px_12px_rgba(0,0,0,0.55)]">
            SI-ADUAN <br>
            <span class="text-2xl md:text-3xl font-semibold text-slate-100 [text-shadow:0_2px_10px_rgba(0,0,0,0.55)]">Sistem Informasi Pengaduan Masyarakat</span>
        </h1>

        <p class="text-slate-100 text-lg max-w-xl mx-auto [text-shadow:0_1px_8px_rgba(0,0,0,0.6)]">
            Sampaikan pengaduan layanan kesehatan Anda
            <br> kami pastikan setiap aduan ditangani tepat sasaran.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center pt-2">
            <a href="{{ route('complaint.create') }}"
               class="grad-btn text-white font-semibold px-8 py-3.5 rounded-xl flex items-center justify-center gap-2 shadow-md hover:shadow-lg active:scale-[0.98] transition-all duration-300">
                <span class="material-icons-outlined text-lg">edit_note</span>
                Ajukan Pengaduan
            </a>
            <a href="{{ route('status.check') }}"
               class="btn-grad-hover bg-white text-navy border border-slate-200 font-semibold px-8 py-3.5 rounded-xl flex items-center justify-center gap-2 hover:text-white hover:border-transparent hover:shadow-md active:scale-[0.98] transition-all duration-300">
                <span class="material-icons-outlined text-lg">search</span>
                Cek Status Tiket
            </a>
        </div>
    </div>

    {{-- Steps --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <h2 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-6">Alur Pengaduan</h2>
        <div class="flex flex-col md:flex-row gap-0">
            @foreach ($steps as $i => $step)
                <div class="flex-1 flex items-start gap-4 relative">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-xl grad-btn flex items-center justify-center shrink-0">
                            <span class="material-icons-outlined text-white text-lg">{{ $step['icon'] }}</span>
                        </div>
                        @if ($i < count($steps) - 1)
                            <div class="hidden md:block absolute top-5 left-10 w-[calc(100%-2.5rem)] h-px bg-slate-100 -z-0"></div>
                        @endif
                    </div>
                    <div class="pb-6 md:pb-0">
                        <div class="text-xs text-slate-400 mb-0.5">Langkah {{ $i + 1 }}</div>
                        <div class="font-bold text-navy text-sm">{{ $step['title'] }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">{{ $step['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-2 sm:gap-4">
        @foreach ($stats as $s)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-3 sm:p-5 text-center">
                <span class="material-icons-outlined text-emerald-600 mb-1 sm:mb-2 block text-xl sm:text-2xl">{{ $s['icon'] }}</span>
                <div class="text-lg sm:text-2xl font-extrabold text-navy">{{ $s['value'] }}</div>
                <div class="text-[10px] sm:text-xs text-slate-500 mt-0.5">{{ $s['label'] }}</div>
            </div>
        @endforeach
    </div>

</div>
@endsection
