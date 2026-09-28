@props(['ikon', 'warna' => 'bg-blue-50 text-blue-600', 'nilai', 'label'])
<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-100 shadow-sm p-5']) }}>
    <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 {{ $warna }}">
        <span class="material-icons-outlined" style="font-size: 18px;">{{ $ikon }}</span>
    </div>
    <div class="text-3xl font-extrabold text-navy">{{ $nilai }}</div>
    <div class="text-xs text-slate-500 mt-0.5">{{ $label }}</div>
    {{ $slot }}
</div>
