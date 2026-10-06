@extends('layouts.admin')

@section('title', 'Profil Saya')
@section('lebar', 'max-w-xl')

@section('content')
@php
    $lencanaPeran = [
        'super_admin' => 'bg-purple-100 text-purple-700',
        'operator'    => 'bg-blue-100 text-blue-700',
        'viewer'      => 'bg-slate-100 text-slate-600',
    ][$pengguna->peran->value] ?? 'bg-slate-100 text-slate-600';
@endphp

<div class="space-y-5">
    <div class="flex items-center gap-3">
        <x-admin.kembali />
        <h1 class="text-2xl font-extrabold text-navy">Profil Saya</h1>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl grad-btn flex items-center justify-center text-white text-xl font-extrabold shrink-0">
                {{ mb_strtoupper(mb_substr($pengguna->name, 0, 1)) }}
            </div>
            <div>
                <div class="font-bold text-base text-navy">{{ $pengguna->name }}</div>
                <div class="text-sm text-slate-500 mt-0.5">{{ $pengguna->email }}</div>
                <span class="inline-block mt-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $lencanaPeran }}">
                    {{ $pengguna->peran->label() }}
                </span>
            </div>
        </div>

        <div class="border-t border-slate-100 pt-4 space-y-3 text-sm">
            @foreach ([
                ['Hak Akses',    $pengguna->peran->deskripsi()],
                ['Unit / Poli',  $pengguna->unit->nama ?? 'Tidak terikat unit tertentu'],
                ['No. WhatsApp', $pengguna->no_wa ?: '—'],
                ['Status Akun',  $pengguna->aktif ? 'Aktif' : 'Nonaktif'],
            ] as [$k, $v])
                <div class="flex gap-4">
                    <span class="text-slate-400 w-32 shrink-0">{{ $k }}</span>
                    <span class="font-medium text-navy">{{ $v }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4 text-sm text-blue-700 flex items-start gap-2">
        <span class="material-icons-outlined shrink-0 mt-0.5" style="font-size: 18px;">shield</span>
        <span>
            @if ($pengguna->adalahSuperAdmin())
                Akun ini memiliki akses penuh ke seluruh fitur SI-ADUAN, termasuk Konfigurasi unit dan staf.
            @else
                Untuk perubahan hak akses atau data akun, hubungi Super Admin.
            @endif
        </span>
    </div>
</div>
@endsection
