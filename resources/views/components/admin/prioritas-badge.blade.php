@props(['prioritas' => null])
@php
    // Bisa berupa enum, teks ('tinggi'/'sedang'/'rendah'), atau kosong (belum digrading).
    $grading = $prioritas instanceof \App\Enums\PrioritasAduan
        ? $prioritas
        : \App\Enums\PrioritasAduan::tryFrom((string) ($prioritas instanceof \BackedEnum ? $prioritas->value : $prioritas));

    $kelas = match ($grading) {
        \App\Enums\PrioritasAduan::Tinggi => 'bg-red-100 text-red-700',
        \App\Enums\PrioritasAduan::Sedang => 'bg-amber-100 text-amber-700',
        \App\Enums\PrioritasAduan::Rendah => 'bg-emerald-100 text-emerald-700',
        default                           => 'bg-slate-100 text-slate-500',
    };
@endphp
<span class="text-xs font-semibold px-2 py-0.5 rounded-full whitespace-nowrap {{ $kelas }}">{{ $grading?->label() ?? 'Belum digrading' }}</span>
