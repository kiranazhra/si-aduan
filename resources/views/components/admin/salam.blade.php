{{-- Sapaan huruf sambung + tanggal. Dipakai di dashboard semua peran (admin, operator, viewer, petugas unit). --}}
<div {{ $attributes->merge(['class' => 'text-right']) }}>
    <p class="font-script text-3xl font-semibold leading-tight text-navy">
        Selamat datang, {{ auth()->user()->name }}
    </p>
    <p class="mt-1.5 text-sm font-medium text-slate-500">
        {{ now()->locale('id')->translatedFormat('l, j F Y') }}
    </p>
</div>
