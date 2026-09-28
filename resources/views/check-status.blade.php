@extends('layouts.app')

@section('title', 'Cek Status Tiket')

@section('content')
@php
    $statusColor = [
        'selesai' => 'bg-emerald-100 text-emerald-700',
        'diproses' => 'bg-blue-100 text-blue-700',
        'dikoordinasikan' => 'bg-blue-100 text-blue-700',
    ];
@endphp

<div class="max-w-2xl mx-auto px-4 py-14 space-y-6">
    <div>
        <div class="text-xs font-bold text-emerald-300 uppercase tracking-widest mb-2 [text-shadow:0_1px_6px_rgba(0,0,0,0.5)]">Lacak Pengaduan</div>
        <h1 class="text-3xl font-extrabold text-navy [text-shadow:0_2px_10px_rgba(0,0,0,0.55)]">Cek Status Tiket</h1>
        <p class="text-slate-100 mt-1 text-sm [text-shadow:0_1px_8px_rgba(0,0,0,0.55)]">Masukkan nomor tiket untuk melihat perkembangan pengaduan.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <form method="GET" action="{{ route('status.check') }}" class="space-y-4">
            <label class="block text-sm font-semibold text-navy">Nomor Tiket</label>
            <div class="flex gap-3">
                <input type="text" name="nomor_tiket" value="{{ $nomorTiket ?? '' }}"
                       placeholder="Contoh: 210926-RI-IGD-K7M2"
                       class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition uppercase">
                <button type="submit"
                        class="grad-btn text-white font-semibold px-6 py-3 rounded-xl flex items-center gap-1.5 transition">
                    <span class="material-icons-outlined text-lg">search</span>
                    Cari
                </button>
            </div>
        </form>
    </div>

    @if (isset($notFound) && $notFound)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center">
            <span class="material-icons-outlined text-slate-300 block mb-3 text-4xl">search_off</span>
            <div class="font-semibold text-slate-600">Tiket tidak ditemukan</div>
            <div class="text-sm text-slate-400 mt-1">Periksa kembali nomor tiket Anda.</div>
        </div>
    @endif

    @if (isset($aduan) && $aduan)
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Nomor Tiket</div>
                        <div class="font-extrabold text-lg font-mono text-navy">{{ $aduan->nomor_tiket }}</div>
                    </div>
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full shrink-0 {{ $statusColor[$aduan->status->value] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $aduan->status->label() }}
                    </span>
                </div>
                <div class="border-t border-slate-50 pt-4 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">Kategori</div>
                        <div class="font-medium text-navy">{{ $aduan->kategori->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-slate-400 mb-0.5">Tanggal Masuk</div>
                        <div class="font-medium text-navy">{{ optional($aduan->dibuat_pada)->translatedFormat('d M Y') }}</div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-xs text-slate-400 mb-0.5">Judul Pengaduan</div>
                        <div class="font-medium text-navy">{{ $aduan->judul }}</div>
                    </div>
                </div>
            </div>

            @if ($aduan->solusi)
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-6 space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                            <span class="material-icons-outlined text-emerald-600 text-base">mark_chat_read</span>
                        </div>
                        <div class="text-sm font-bold text-navy">Tanggapan Rumah Sakit</div>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $aduan->solusi }}</p>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400 border-t border-slate-100 pt-3">
                        <span class="material-icons-outlined text-sm">schedule</span>
                        Tanggapan dikirim pada {{ optional($aduan->solusi_dikirim_pada)->translatedFormat('d M Y \\· H.i') }}
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-3 text-sm text-slate-500">
                    <span class="material-icons-outlined text-slate-300 shrink-0 text-xl">hourglass_top</span>
                    <div>
                        <div class="font-semibold text-slate-600">Pengaduan sedang ditangani</div>
                        <div class="text-xs mt-0.5">Tanggapan dari rumah sakit akan muncul di sini setelah aduan diselesaikan.</div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
