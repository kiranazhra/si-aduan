@extends('layouts.admin')

@section('title', $judul)

@section('content')
<div class="space-y-6">
    <x-admin.judul :judul="$judul" sub="Halaman ini sedang disiapkan." />

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-10 text-center">
        <div class="w-12 h-12 rounded-2xl grad-btn flex items-center justify-center mx-auto mb-4">
            <span class="material-icons-outlined text-white" style="font-size: 24px;">construction</span>
        </div>
        <div class="text-base font-bold text-navy">Segera hadir</div>
        <div class="text-sm text-slate-500 mt-1">Menu ini akan tersedia pada pembaruan berikutnya.</div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 mt-5 text-sm font-semibold text-emerald-700 hover:underline">
            <span class="material-icons-outlined" style="font-size: 16px;">arrow_back</span>Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
