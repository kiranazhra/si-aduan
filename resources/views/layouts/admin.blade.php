@php
    $pengguna = auth()->user();
    $peran    = $pengguna->peran;

    $menu = $pengguna->bolehMenanggapi()
        ? [
            // Menu Petugas Unit
            ['ikon' => 'dashboard',  'label' => 'Dashboard',       'rute' => 'unit.dashboard', 'aktif' => 'unit.dashboard'],
            ['ikon' => 'inbox',      'label' => 'Tiket Unit Saya', 'rute' => 'unit.tickets',   'aktif' => 'unit.tickets*'],
            ['ikon' => 'history',    'label' => 'Riwayat Selesai', 'rute' => 'unit.history',   'aktif' => 'unit.history*'],
            ['ikon' => 'person',     'label' => 'Profil Saya',     'rute' => 'unit.profile',   'aktif' => 'unit.profile*'],
        ]
        : [
            ['ikon' => 'dashboard',  'label' => 'Dashboard',   'rute' => 'admin.dashboard', 'aktif' => 'admin.dashboard'],
            ['ikon' => 'inbox',      'label' => 'Semua Tiket', 'rute' => 'admin.tickets',   'aktif' => 'admin.tickets*'],
            ['ikon' => 'assessment', 'label' => 'Rekap',       'rute' => 'admin.recap',     'aktif' => 'admin.recap*'],
            ['ikon' => 'settings',   'label' => 'Konfigurasi', 'rute' => 'admin.config',    'aktif' => 'admin.config*'],
            ['ikon' => 'person',     'label' => 'Profil Saya', 'rute' => 'admin.profile',   'aktif' => 'admin.profile*'],
        ];

    $lencanaPeran = [
        'super_admin'  => 'bg-purple-100 text-purple-700',
        'operator'     => 'bg-blue-100 text-blue-700',
        'petugas_unit' => 'bg-emerald-100 text-emerald-700',
        'viewer'       => 'bg-slate-100 text-slate-600',
    ][$peran->value] ?? 'bg-slate-100 text-slate-600';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .kop-cetak { display: none !important; }
        .ttd-cetak { display: none !important; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }

            body { background: #fff !important; }
            .no-print { display: none !important; }
            .kop-cetak { display: block !important; }
            .ttd-cetak {
                display: flex !important;
                justify-content: flex-end !important;
                margin-top: 24px !important;
            }

            /* Halaman cetak pakai lebar penuh, bukan lebar layar (max-w-*) */
            main > div[class^="max-w-"] { max-width: 100% !important; }

            /* Tabel rekap: satu halaman, tanpa scroll ke samping, bentuk kotak default */
            .rekap-tabel {
                background: #fff !important;
                border: 1px solid #000 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
            }
            .rekap-tabel .overflow-x-auto { overflow: visible !important; }
            .rekap-tabel table {
                width: 100% !important;
                table-layout: auto !important;
                border-collapse: collapse !important;
                font-size: 10px !important;
            }
            .rekap-tabel th,
            .rekap-tabel td {
                border: 1px solid #000 !important;
                padding: 4px 6px !important;
                white-space: normal !important;
                word-break: break-word;
                color: #000 !important;
                background: #fff !important;
            }
            .rekap-tabel thead th {
                background: #f1f5f9 !important;
                font-weight: 700 !important;
            }
            .rekap-tabel tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen"
      x-data="{ menu: false, collapsed: localStorage.getItem('sidebarCollapsed') === '1' }"
      x-init="$watch('collapsed', v => localStorage.setItem('sidebarCollapsed', v ? '1' : '0'))"
      @keydown.escape.window="menu = false">

<div class="min-h-screen flex flex-col md:flex-row">

    {{-- Bar atas (khusus layar kecil) --}}
    <div class="md:hidden no-print bg-white border-b border-slate-100 px-4 py-2.5 flex items-center justify-between sticky top-0 z-30">
        <button type="button" @click="menu = true" class="text-slate-600" aria-label="Buka menu">
            <span class="material-icons-outlined">menu</span>
        </button>
        <img src="{{ asset('images/logo-rsud.png') }}" alt="SI-ADUAN" class="h-8 w-auto object-contain">
        <span class="w-6"></span>
    </div>

    {{-- Latar gelap saat menu terbuka (layar kecil) --}}
    <div x-show="menu" x-cloak @click="menu = false" class="md:hidden no-print fixed inset-0 z-30 bg-slate-900/30"></div>

    {{-- Sidebar --}}
    <div class="relative no-print shrink-0" :class="collapsed ? 'md:w-[76px]' : 'md:w-56'">
    <aside class="w-56 border-r border-slate-100 flex-col overflow-y-auto md:sticky md:top-0 md:h-screen relative transition-all duration-300"
           :class="[menu ? 'flex fixed inset-y-0 left-0 z-40 md:static' : 'hidden md:flex', collapsed ? 'md:w-[76px]' : 'md:w-56']">

        {{-- Foto latar, menutupi seluruh tinggi sidebar dari atas sampai bawah --}}
        <img src="{{ asset('images/sidebar-bg.jpg') }}" alt=""
             class="absolute inset-0 w-full h-full object-cover pointer-events-none select-none" style="z-index: 0;">
        <div class="absolute inset-0" style="z-index: 1; background: linear-gradient(180deg, rgba(13,42,84,0.62) 0%, rgba(15,46,90,0.72) 100%);"></div>

        <div class="relative flex flex-col h-full" style="z-index: 10;">

        {{-- Logo --}}
        <div class="px-4 py-4 border-b border-white/15 flex items-center gap-2.5" :class="collapsed ? 'justify-center px-2' : ''">
            <img src="{{ asset('images/logo-rsud.png') }}" alt="SI-ADUAN" class="h-10 w-auto object-contain shrink-0 drop-shadow">
            <div x-show="!collapsed" x-cloak>
                <div class="text-lg font-extrabold text-white leading-tight">SI-ADUAN</div>
                <div class="text-[10px] text-white/70 leading-snug">Sistem Informasi Aduan Masyarakat RSUD H. Damanhuri Barabai</div>
            </div>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 p-3 space-y-0.5">
            @foreach ($menu as $m)
                @php $aktif = request()->routeIs($m['aktif']); @endphp
                <a href="{{ route($m['rute']) }}" title="{{ $m['label'] }}"
                   class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all {{ $aktif ? 'grad-btn text-white font-semibold shadow-sm' : 'text-white/85 hover:bg-white/10 font-medium' }}"
                   :class="collapsed ? 'justify-center px-0' : ''">
                    <span class="material-icons-outlined shrink-0" style="font-size: 18px;">{{ $m['ikon'] }}</span>
                    <span x-show="!collapsed" x-cloak>{{ $m['label'] }}</span>
                </a>
            @endforeach

            @if ($pengguna->hanyaLihat())
                <div class="mt-3 px-3 py-2 bg-white/10 rounded-xl text-xs text-white/70 leading-snug" x-show="!collapsed" x-cloak>
                    <span class="material-icons-outlined block mb-1 text-white/50" style="font-size: 14px;">visibility</span>
                    Anda hanya bisa melihat data tanpa mengubah apa pun.
                </div>
            @endif
        </nav>

        {{-- Info pengguna --}}
        <div class="p-3 border-t border-white/15 space-y-2">
            <div :class="collapsed ? 'text-center' : ''" x-show="!collapsed" x-cloak>
                <div class="text-sm font-semibold text-white truncate px-1">{{ $pengguna->name }}</div>
                <div class="text-xs text-white/60 truncate px-1">{{ $peran->label() }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-white/90 hover:text-white bg-white/10 hover:bg-white/20 border border-white/15 rounded-lg transition"
                        :class="collapsed ? 'justify-center px-0' : ''">
                    <span class="material-icons-outlined shrink-0" style="font-size: 16px;">logout</span>
                    <span x-show="!collapsed" x-cloak>Logout</span>
                </button>
            </form>
        </div>
        </div>
    </aside>

    {{-- Tombol minimize/expand (desktop), ditaruh di luar <aside> supaya tidak kepotong overflow --}}
    <button type="button" @click="collapsed = !collapsed"
            class="no-print hidden md:flex items-center justify-center w-6 h-6 rounded-full bg-white border border-slate-200 shadow-md text-slate-500 hover:text-navy hover:border-navy transition-all absolute top-5 -right-3 z-20"
            :aria-label="collapsed ? 'Perbesar sidebar' : 'Ciutkan sidebar'">
        <span class="material-icons-outlined" style="font-size: 14px;" x-text="collapsed ? 'chevron_right' : 'chevron_left'"></span>
    </button>
    </div>

    {{-- Konten --}}
    <main class="flex-1 min-w-0 p-4 md:p-6 print:p-0">
        @if (session('success') || session('error') || $errors->any())
            <div class="@yield('lebar', 'max-w-5xl') mx-auto mb-4 space-y-3">
                @if (session('success'))
                    <div class="no-print flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
                        <span class="material-icons-outlined" style="font-size: 18px;">check_circle</span>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="no-print flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                        <span class="material-icons-outlined" style="font-size: 18px;">error_outline</span>
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="no-print bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                        <div class="font-semibold mb-1">Data belum dapat disimpan:</div>
                        <ul class="list-disc pl-5 space-y-0.5 text-xs">
                            @foreach ($errors->all() as $pesan)
                                <li>{{ $pesan }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        <div class="@yield('lebar', 'max-w-5xl') mx-auto">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
<script>
    // Tombol "PDF" di berkas rekap membuka halaman dengan ?cetak=1 lalu langsung mencetak
    if (new URLSearchParams(location.search).has('cetak')) {
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 500); });
    }
</script>
</body>
</html>
