@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
@php
    // Sesuai banner resmi "Penanganan Pengaduan" RSUD H. Damanhuri Barabai
    $hotline = '085249808800';
    $hotlineTampil = '0852-4980-8800';
    $hotlineWa = '62' . substr($hotline, 1);

    $kanal = [
        [
            'icon' => 'call',
            'label' => 'Hotline Service',
            'value' => 'Telepon/WA : ' . $hotlineTampil,
            'href' => 'https://wa.me/' . $hotlineWa,
        ],
        [
            'icon' => 'mail',
            'label' => 'Email',
            'value' => 'rshd@hstkab.go.id',
            'href' => 'mailto:rshd@hstkab.go.id',
        ],
        [
            'icon' => 'inbox',
            'label' => 'Kotak Saran',
            'value' => 'Di tiap-tiap Area Pelayanan',
        ],
        [
            'icon' => 'apps',
            'label' => 'Aplikasi',
            'list' => [
                'Aplikasi APAM',
                ['SP4N-LAPOR! : lapor.go.id', 'https://lapor.go.id'],
                'Website',
            ],
        ],
        [
            'icon' => 'share',
            'label' => 'Media Sosial',
            'value' => 'Instagram, Youtube, Tiktok, Facebook, Google Review',
        ],
        [
            'icon' => 'storefront',
            'label' => 'Langsung',
            'value' => 'Secara langsung ke Unit Pengaduan',
        ],
        [
            'icon' => 'phone_in_talk',
            'label' => 'Telepon Pejabat Ruangan',
            'value' => 'Melalui nomor telepon Direktur, Kepala Bidang, Kepala Seksi dan Kepala Ruangan terkait yang terpasang pada papan informasi di setiap ruang rawat inap.',
        ],
    ];
@endphp

<div class="max-w-4xl mx-auto px-4 py-14 space-y-8">
    <div>
        <div class="text-xs font-bold text-emerald-300 uppercase tracking-widest mb-2 [text-shadow:0_1px_6px_rgba(0,0,0,0.5)]">Kontak</div>
        <h1 class="text-3xl font-extrabold text-navy [text-shadow:0_2px_10px_rgba(0,0,0,0.55)]">Penanganan Pengaduan</h1>
        <p class="text-slate-100 mt-2 [text-shadow:0_1px_8px_rgba(0,0,0,0.55)]">Sampaikan pengaduan Anda melalui salah satu kanal berikut.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="font-bold text-navy mb-4 flex items-center gap-2">
            <span class="material-icons-outlined text-emerald-600" style="font-size: 20px;">support_agent</span>
            Kanal Pengaduan
        </div>
        <div class="columns-1 sm:columns-2 gap-x-8">
            @foreach ($kanal as $k)
                <div class="flex items-start gap-2.5 break-inside-avoid mb-4 last:mb-0">
                    <span class="material-icons-outlined text-slate-400 shrink-0" style="font-size: 20px;">{{ $k['icon'] }}</span>
                    <div class="text-sm">
                        <div class="font-semibold text-navy">{{ $k['label'] }}</div>

                        @isset($k['list'])
                            <ol class="text-slate-500 text-xs list-decimal list-inside space-y-0.5">
                                @foreach ($k['list'] as $item)
                                    @if (is_array($item))
                                        <li><a href="{{ $item[1] }}" target="_blank" rel="noopener" class="text-[#1565C0] hover:underline">{{ $item[0] }}</a></li>
                                    @else
                                        <li>{{ $item }}</li>
                                    @endif
                                @endforeach
                            </ol>
                        @elseif (! empty($k['href']))
                            <a href="{{ $k['href'] }}" target="_blank" rel="noopener" class="text-[#1565C0] hover:underline text-xs">{{ $k['value'] }}</a>
                        @else
                            <div class="text-slate-500 text-xs">{{ $k['value'] }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
