{{-- Diagram donat SVG tanpa library. $items = [['name'=>..., 'value'=>..., 'color'=>'#hex'], ...] --}}
@props(['items', 'ukuran' => 130, 'tebal' => 22])
@php
    $total  = collect($items)->sum('value');
    $radius = ($ukuran - $tebal) / 2;
    $keliling = 2 * M_PI * $radius;
    $tengah = $ukuran / 2;
    $geser  = 0;
@endphp
<svg width="{{ $ukuran }}" height="{{ $ukuran }}" viewBox="0 0 {{ $ukuran }} {{ $ukuran }}" class="shrink-0 -rotate-90" role="img" aria-label="Diagram">
    <circle cx="{{ $tengah }}" cy="{{ $tengah }}" r="{{ $radius }}" fill="none" stroke="#f1f5f9" stroke-width="{{ $tebal }}"></circle>
    @foreach ($items as $d)
        @php $panjang = $total > 0 ? $d['value'] / $total * $keliling : 0; @endphp
        @if ($panjang > 0)
            <circle cx="{{ $tengah }}" cy="{{ $tengah }}" r="{{ $radius }}" fill="none"
                    stroke="{{ $d['color'] }}" stroke-width="{{ $tebal }}"
                    stroke-dasharray="{{ round($panjang, 3) }} {{ round($keliling - $panjang, 3) }}"
                    stroke-dashoffset="{{ round(-$geser, 3) }}">
                <title>{{ $d['name'] }}: {{ $d['value'] }} tiket</title>
            </circle>
        @endif
        @php $geser += $panjang; @endphp
    @endforeach
</svg>
