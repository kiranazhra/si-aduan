@props(['judul', 'sub' => null, 'kembali' => null, 'sejajar' => false])
{{-- $sejajar: true = slot (mis. filter) ditaruh sebaris di kanan judul pada layar lebar; subjudul yang menyesuaikan lebar. --}}
{{-- $kembali: null = tombol kembali otomatis, false = sembunyikan, atau isi URL tujuan bila asal tidak diketahui. --}}
<div class="flex flex-wrap justify-between gap-3 {{ $sejajar ? 'items-center lg:flex-nowrap' : 'items-start' }}">
    <div class="flex items-start gap-3 {{ $sejajar ? 'min-w-0' : '' }}">
        @if ($kembali !== false)
            <x-admin.kembali :ke="$kembali" />
        @endif
        <div class="{{ $sejajar ? 'min-w-0' : '' }}">
            <h1 class="text-2xl font-extrabold text-navy">{{ $judul }}</h1>
            @if ($sub)
                <p class="text-sm text-slate-500 mt-0.5">{{ $sub }}</p>
            @endif
        </div>
    </div>
    @if (! $slot->isEmpty())
        <div class="{{ $sejajar ? 'shrink-0' : '' }}">{{ $slot }}</div>
    @endif
</div>
