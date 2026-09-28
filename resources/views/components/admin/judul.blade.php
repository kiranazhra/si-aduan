@props(['judul', 'sub' => null, 'kembali' => null])
{{-- $kembali: null = tombol kembali otomatis, false = sembunyikan, atau isi URL tujuan bila asal tidak diketahui. --}}
<div class="flex flex-wrap items-start justify-between gap-3">
    <div class="flex items-start gap-3">
        @if ($kembali !== false)
            <x-admin.kembali :ke="$kembali" />
        @endif
        <div>
            <h1 class="text-2xl font-extrabold text-navy">{{ $judul }}</h1>
            @if ($sub)
                <p class="text-sm text-slate-500 mt-0.5">{{ $sub }}</p>
            @endif
        </div>
    </div>
    @if (! $slot->isEmpty())
        <div>{{ $slot }}</div>
    @endif
</div>
