@extends('layouts.app')

@section('title', 'Panduan')

@section('content')
@php
    $steps = [
        ['icon' => 'edit_note', 'title' => 'Isi Formulir Pengaduan', 'desc' => 'Masukkan nama, nomor WhatsApp, kategori, unit/poli, dan uraian pengaduan Anda.'],
        ['icon' => 'upload_file', 'title' => 'Lampirkan Bukti (Opsional)', 'desc' => 'Unggah foto atau dokumen pendukung jika ada, maksimal 5 MB.'],
        ['icon' => 'send', 'title' => 'Kirim & Simpan Nomor Tiket', 'desc' => 'Setelah dikirim, catat nomor tiket untuk memantau perkembangan penanganan.'],
        ['icon' => 'notifications_active', 'title' => 'Ikuti Perkembangan', 'desc' => 'Cek status pengaduan melalui menu "Cek Status" kapan saja menggunakan nomor tiket.'],
    ];

    $jangkaWaktu = [
        ['warna' => 'bg-red-500', 'card' => 'bg-red-50 border-red-200', 'judul' => 'text-red-700', 'ket_warna' => 'text-red-500', 'label' => 'Merah', 'waktu' => '1 × 24 Jam', 'ket' => 'Pengaduan berat/mendesak'],
        ['warna' => 'bg-amber-400', 'card' => 'bg-amber-50 border-amber-200', 'judul' => 'text-amber-700', 'ket_warna' => 'text-amber-600', 'label' => 'Kuning', 'waktu' => '3 Hari Kerja', 'ket' => 'Pengaduan sedang'],
        ['warna' => 'bg-emerald-500', 'card' => 'bg-emerald-50 border-emerald-200', 'judul' => 'text-emerald-700', 'ket_warna' => 'text-emerald-600', 'label' => 'Hijau', 'waktu' => '7 Hari Kerja', 'ket' => 'Pengaduan ringan'],
    ];

    $persyaratan = [
        'Mengisi formulir pengaduan (tertulis atau elektronik) baik langsung ke unit informasi & pengaduan maupun lewat kotak saran.',
        'Menyebutkan nama/identitas pelapor (boleh anonim, namun lebih dianjurkan mencantumkan identitas yang jelas).',
        'Menyertakan kronologis pengaduan beserta bukti pendukung bila ada.',
    ];

    $prosedur = [
        'Pasien/keluarga menyampaikan pengaduan secara lisan atau tertulis melalui media sosial resmi RS, langsung ke meja pengaduan, kotak saran, atau aplikasi online (termasuk SI-ADUAN ini).',
        'Pengaduan lisan langsung dilayani pada jam kerja: Senin–Kamis 08.00–15.00 WITA, Jumat 07.30–11.00 WITA, Sabtu 08.00–14.00 WITA.',
        'Pelapor melakukan verifikasi terhadap laporan yang telah dicatat sebelum dimasukkan ke buku pengaduan.',
        'Pengaduan diteruskan ke unit terkait untuk ditindaklanjuti. Feedback hasil tindak lanjut disampaikan maksimal 3×24 jam untuk pengaduan ringan dan 7×24 jam untuk pengaduan sedang/berat.',
    ];

    $faqs = [
        ['q' => 'Berapa lama pengaduan akan ditangani?', 'a' => 'Sesuai standar pelayanan: pengaduan kategori Merah (berat/mendesak) 1×24 jam, Kuning (sedang) 3 hari kerja, dan Hijau (ringan) 7 hari kerja sejak diterima.'],
        ['q' => 'Apakah ada biaya untuk mengajukan pengaduan?', 'a' => 'Tidak ada biaya sama sekali — layanan pengaduan RSUD H. Damanhuri Barabai sepenuhnya gratis.'],
        ['q' => 'Apakah saya bisa melacak pengaduan tanpa akun?', 'a' => 'Bisa, cukup gunakan nomor tiket yang diberikan setelah pengaduan berhasil dikirim.'],
        ['q' => 'Pengaduan apa saja yang bisa disampaikan?', 'a' => 'Segala hal terkait pelayanan rumah sakit: fasilitas, tenaga medis, administrasi, dan kebersihan — dalam bentuk saran, masukan, solusi, maupun rekomendasi.'],
    ];
@endphp

<div class="max-w-4xl mx-auto px-4 py-14 space-y-12">
    <div>
        <div class="text-xs font-bold text-emerald-300 uppercase tracking-widest mb-2 [text-shadow:0_1px_6px_rgba(0,0,0,0.5)]">Panduan</div>
        <h1 class="text-3xl font-extrabold text-navy [text-shadow:0_2px_10px_rgba(0,0,0,0.55)]">Cara Mengajukan Pengaduan</h1>
        <p class="text-slate-100 mt-2 [text-shadow:0_1px_8px_rgba(0,0,0,0.55)]">Ikuti langkah berikut untuk menyampaikan pengaduan dengan mudah.</p>
    </div>

    <div class="space-y-4">
        @foreach ($steps as $i => $step)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl grad-btn flex items-center justify-center shrink-0">
                    <span class="material-icons-outlined text-white text-lg">{{ $step['icon'] }}</span>
                </div>
                <div>
                    <div class="text-xs text-slate-400 mb-0.5">Langkah {{ $i + 1 }}</div>
                    <div class="font-bold text-navy">{{ $step['title'] }}</div>
                    <div class="text-sm text-slate-500 mt-0.5">{{ $step['desc'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <a href="{{ route('complaint.create') }}"
       class="grad-btn text-white font-semibold px-8 py-3.5 rounded-xl inline-flex items-center gap-2 shadow-md hover:shadow-lg transition-all">
        <span class="material-icons-outlined text-lg">edit_note</span>
        Mulai Ajukan Pengaduan
    </a>

    {{-- Standar Pelayanan --}}
    <div>
        <div style="color:#2dd4bf" class="text-xs font-bold uppercase tracking-widest mb-2 [text-shadow:0_1px_6px_rgba(0,0,0,0.5)]">Standar Pelayanan</div>
        <h2 class="text-xl font-bold text-navy mb-1">Standar Pelayanan Unit Pengaduan</h2>
        <p class="text-sm text-white mb-5 [text-shadow:0_1px_8px_rgba(0,0,0,0.55)]">Produk layanan: Jasa Konsultasi (saran, masukan, solusi, dan rekomendasi) — <span style="color:#2dd4bf" class="font-semibold">tidak dipungut biaya / gratis</span>.</p>

        {{-- Jangka waktu --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
            @foreach ($jangkaWaktu as $j)
                <div class="{{ $j['card'] }} rounded-2xl border shadow-sm p-4 flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full {{ $j['warna'] }} shrink-0"></span>
                    <div>
                        <div class="text-sm font-bold {{ $j['judul'] }}">{{ $j['label'] }} · {{ $j['waktu'] }}</div>
                        <div class="text-xs {{ $j['ket_warna'] }}">{{ $j['ket'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Persyaratan --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-4">
            <div class="font-bold text-navy mb-3 flex items-center gap-2">
                <span class="material-icons-outlined text-emerald-600" style="font-size: 20px;">fact_check</span>
                Persyaratan Pengaduan
            </div>
            <ol class="space-y-2 text-sm text-slate-600 list-decimal list-inside">
                @foreach ($persyaratan as $p)
                    <li>{{ $p }}</li>
                @endforeach
            </ol>
        </div>

        {{-- Prosedur --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="font-bold text-navy mb-3 flex items-center gap-2">
                <span class="material-icons-outlined text-emerald-600" style="font-size: 20px;">settings_suggest</span>
                Sistem, Mekanisme &amp; Prosedur Pengaduan
            </div>
            <ol class="space-y-2 text-sm text-slate-600 list-decimal list-inside">
                @foreach ($prosedur as $p)
                    <li>{{ $p }}</li>
                @endforeach
            </ol>
        </div>
    </div>

    <div>
        <h2 class="text-xl font-bold text-navy mb-4">Pertanyaan Umum</h2>
        <div class="space-y-2">
            @foreach ($faqs as $i => $faq)
                <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <button @click="open = !open" type="button"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="font-semibold text-sm text-navy">{{ $faq['q'] }}</span>
                        <span class="material-icons-outlined text-slate-400 transition-transform text-lg"
                              :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-collapse class="px-5 pb-4 text-sm text-slate-500 border-t border-slate-50 pt-3">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
