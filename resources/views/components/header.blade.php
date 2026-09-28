@php
    $navLinks = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'guide', 'label' => 'Panduan'],
        ['route' => 'complaint.create', 'label' => 'Ajukan Pengaduan'],
        ['route' => 'status.check', 'label' => 'Cek Status'],
        ['route' => 'contact', 'label' => 'Kontak'],
    ];
@endphp

<header class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-50" x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <div class="max-w-6xl mx-auto px-4 md:px-6 py-2.5 md:py-3 flex items-center justify-between md:grid md:grid-cols-3">

        {{-- Kiri: Logo --}}
        <a href="{{ route('home') }}" class="flex items-center md:justify-self-start" @click="menu = false">
            <img src="{{ asset('images/logo-rsud.png') }}" alt="SI-ADUAN" class="h-11 md:h-16 w-auto object-contain">
        </a>

        {{-- Tengah: Nav (layar besar) --}}
        <nav class="hidden md:flex items-center justify-center gap-7 justify-self-center">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}"
                   class="nav-link text-sm pb-1 whitespace-nowrap transition-colors {{ request()->routeIs($link['route']) ? 'active font-semibold text-[#1565C0]' : 'text-slate-600 hover:text-[#1565C0] font-medium' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Kanan: Tombol Admin + tombol menu (layar kecil) --}}
        <div class="flex items-center gap-2 md:justify-self-end">
            <a href="{{ auth()->check() ? route('admin.dashboard') : route('login') }}"
               class="grad-btn text-white text-xs font-semibold rounded-xl px-3.5 py-2 md:px-4 transition-all shadow-sm hover:shadow-md whitespace-nowrap">
                {{ auth()->check() ? 'Admin' : 'Masuk Admin' }}
            </a>
            <button type="button" @click="menu = !menu" class="md:hidden text-slate-600 p-1 -mr-1" :aria-expanded="menu.toString()" aria-label="Buka menu navigasi">
                <span class="material-icons-outlined" x-text="menu ? 'close' : 'menu'"></span>
            </button>
        </div>
    </div>

    {{-- Menu navigasi (layar kecil) --}}
    <nav x-show="menu" x-cloak x-transition.opacity.duration.150ms
         class="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
        @foreach ($navLinks as $link)
            <a href="{{ route($link['route']) }}" @click="menu = false"
               class="block px-3 py-2.5 rounded-xl text-sm transition-colors {{ request()->routeIs($link['route']) ? 'bg-blue-50 text-[#1565C0] font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>
