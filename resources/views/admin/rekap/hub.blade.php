@extends('layouts.admin')

@section('title', 'Rekap & Laporan')

@section('content')
@php
    $bagian = [
        ['ikon' => 'bar_chart', 'label' => 'Data Aduan',       'ket' => 'Statistik dan tren keseluruhan pengaduan',  'rute' => 'admin.recap.data'],
        ['ikon' => 'category',  'label' => 'Kategori',         'ket' => 'Distribusi pengaduan per kategori layanan', 'rute' => 'admin.recap.category'],
        ['ikon' => 'grade',     'label' => 'Grading Unit',     'ket' => 'Penilaian kinerja setiap unit pelayanan',   'rute' => 'admin.recap.grading'],
        ['ikon' => 'star',      'label' => 'Rating Pelayanan', 'ket' => 'Penilaian bintang dari pelapor, per unit',  'rute' => 'admin.recap.rating'],
        ['ikon' => 'place',     'label' => 'Lokasi / Ruangan', 'ket' => 'Distribusi pengaduan per area rumah sakit', 'rute' => 'admin.recap.room'],
        ['ikon' => 'insights',  'label' => 'Kesimpulan',       'ket' => 'Analisis dan rekomendasi tindak lanjut',    'rute' => 'admin.recap.conclusion'],
        ['ikon' => 'folder_open', 'label' => 'Berkas Rekap',   'ket' => 'Unduh laporan Excel dan PDF',               'rute' => 'admin.recap.files'],
    ];
@endphp

<div class="space-y-6">
    <x-admin.judul judul="Rekap & Laporan" sub="Pilih laporan yang ingin ditampilkan" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($bagian as $b)
            <a href="{{ route($b['rute'], ['tahun' => $tahun]) }}"
               class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 text-left hover:border-navy hover:shadow-md transition-all group">
                <div class="w-10 h-10 rounded-xl grad-btn flex items-center justify-center mb-3 group-hover:opacity-90 transition">
                    <span class="material-icons-outlined text-white" style="font-size: 20px;">{{ $b['ikon'] }}</span>
                </div>
                <div class="text-sm font-bold mb-1 text-navy">{{ $b['label'] }}</div>
                <div class="text-xs text-slate-500 leading-snug">{{ $b['ket'] }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
