@props(['status'])
@php
    $status = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $kelas = [
        'selesai'         => 'bg-emerald-100 text-emerald-700',
        'diproses'        => 'bg-amber-100 text-amber-700',
        'dikoordinasikan' => 'bg-blue-100 text-blue-700',
    ][$status] ?? 'bg-slate-100 text-slate-600';
    $label = \App\Enums\StatusAduan::tryFrom((string) $status)?->label() ?? ucfirst((string) $status);
@endphp
<span class="text-xs font-semibold px-2.5 py-1 rounded-full whitespace-nowrap {{ $kelas }}">{{ $label }}</span>
