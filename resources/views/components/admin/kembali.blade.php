{{--
    Tombol kembali di samping judul halaman. Hanya untuk sub-halaman yang punya halaman induk selain
    Dashboard (saat ini: sub-halaman Rekap, kembali ke Rekap). Tombol yang tujuannya Dashboard tidak ditampilkan.
      - Kembali ke halaman panel tempat pengguna berasal, kecuali asalnya Dashboard atau halaman yang sama.
      - Bila tidak ada asal (dibuka langsung, dari luar panel, atau hanya mengganti filter), menuju halaman induk.
    Pemakaian: otomatis lewat <x-admin.judul>, atau <x-admin.kembali :ke="route('admin.tickets')" />
--}}
@props(['ke' => null])
@php
    $tujuan = $ke ?? (request()->routeIs('admin.recap.*') ? route('admin.recap') : null);

    // Jangan tampilkan tombol yang tujuannya Dashboard.
    $tampil = $tujuan !== null && ! in_array($tujuan, [route('admin.dashboard'), route('unit.dashboard')], true);
@endphp
@if ($tampil)
    <a href="{{ $tujuan }}" title="Kembali" aria-label="Kembali"
       onclick="try{var u=new URL(document.referrer);if(u.origin===location.origin&&/^\/(admin|unit)(\/|$)/.test(u.pathname)&&!/\/dashboard$/.test(u.pathname)&&u.pathname!==location.pathname){location.href=document.referrer;return false;}}catch(e){}return true;"
       class="shrink-0 mt-0.5 inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:border-navy hover:text-navy hover:bg-slate-50 transition print:hidden">
        <span class="material-icons-outlined" style="font-size: 22px;">arrow_back</span>
    </a>
@endif
