@extends('layouts.app')

@section('title', 'Konfirmasi Tiket')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-20 text-center">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 space-y-6">
        <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mx-auto">
            <span class="material-icons-outlined text-emerald-600 text-3xl">check_circle</span>
        </div>
        <div>
            <div class="text-sm text-slate-500 mb-2">Pengaduan berhasil dikirim</div>
            <div class="text-xs text-slate-400 uppercase tracking-widest mb-3">Nomor Tiket Anda</div>
            <div class="text-2xl sm:text-3xl font-extrabold tracking-wide text-navy break-all">{{ $ticket }}</div>
        </div>
        <div class="bg-amber-50 border border-amber-100 rounded-xl px-5 py-4 text-sm text-amber-700 flex items-start gap-2 text-left">
            <span class="material-icons-outlined shrink-0 mt-0.5 text-base">info</span>
            Simpan nomor tiket ini — gunakan untuk memantau status pengaduan Anda di halaman Cek Status.
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('status.check', ['nomor_tiket' => $ticket]) }}"
               class="flex-1 grad-btn text-white font-semibold py-3.5 rounded-xl flex items-center justify-center gap-2">
                <span class="material-icons-outlined text-base">search</span>
                Cek Status Sekarang
            </a>
            <a href="{{ route('home') }}"
               class="flex-1 border border-slate-200 text-slate-600 font-semibold py-3.5 rounded-xl hover:border-slate-300 transition text-center">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
