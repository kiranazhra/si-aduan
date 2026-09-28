{{-- Deretan 5 bintang. $nilai boleh desimal (mis. 4.3): bintang terisi sesuai proporsi. --}}
@props(['nilai' => 0, 'ukuran' => 'w-4 h-4'])

@php
    $nilai = (float) ($nilai ?? 0);
    $pct   = max(0, min(100, $nilai / 5 * 100));
    $path  = 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z';
@endphp

<span class="relative inline-flex align-middle" role="img" aria-label="{{ number_format($nilai, 1) }} dari 5 bintang">
    <span class="flex text-slate-200">
        @for ($i = 0; $i < 5; $i++)
            <svg viewBox="0 0 24 24" class="{{ $ukuran }} shrink-0" fill="currentColor"><path d="{{ $path }}"/></svg>
        @endfor
    </span>
    <span class="absolute inset-y-0 left-0 overflow-hidden flex text-amber-400" style="width: {{ $pct }}%;">
        @for ($i = 0; $i < 5; $i++)
            <svg viewBox="0 0 24 24" class="{{ $ukuran }} shrink-0" fill="currentColor"><path d="{{ $path }}"/></svg>
        @endfor
    </span>
</span>
