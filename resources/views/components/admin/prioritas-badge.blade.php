@props(['prioritas'])
@php
    $prioritas = $prioritas instanceof \BackedEnum ? $prioritas->value : (string) $prioritas;
    $kelas = [
        'tinggi' => 'bg-red-100 text-red-700',
        'sedang' => 'bg-amber-100 text-amber-700',
        'rendah' => 'bg-slate-100 text-slate-500',
    ][$prioritas] ?? 'bg-slate-100 text-slate-500';
@endphp
<span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap {{ $kelas }}">{{ ucfirst((string) $prioritas) }}</span>
