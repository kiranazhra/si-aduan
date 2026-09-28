@php
    $footerLinks = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'guide', 'label' => 'Panduan'],
        ['route' => 'complaint.create', 'label' => 'Ajukan Pengaduan'],
        ['route' => 'status.check', 'label' => 'Cek Status'],
        ['route' => 'contact', 'label' => 'Kontak'],
    ];

    $urlMaps = 'https://www.google.com/maps/search/?api=1&query=RSUD+H.+Damanhuri+Barabai';
    $embedMaps = 'https://www.google.com/maps?q=RSUD+H.+Damanhuri+Barabai&output=embed';
@endphp

<footer class="bg-white border-t border-slate-100 mt-auto">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-[auto_1fr_auto_15rem] items-start gap-6">

            {{-- Brand + sosial media --}}
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl white-btn flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/logo_rshd.png') }}" alt="RSHD" class="h-9 w-auto object-contain">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-navy">SI-ADUAN</div>
                        <div class="text-xs text-slate-500">RSUD H. Damanhuri Barabai</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <a href="https://www.instagram.com/rshdbarabai/" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram"
                       class="w-9 h-9 rounded-full text-white flex items-center justify-center shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all"
                       style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.6" fill="currentColor"/></svg>
                    </a>
                    <a href="https://www.tiktok.com/@rshdbarabaiofficial" target="_blank" rel="noopener" aria-label="TikTok" title="TikTok"
                       class="w-9 h-9 rounded-full text-white flex items-center justify-center shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all"
                       style="background: #010101;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-[16px] h-[16px]" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                    <a href="{{ $urlMaps }}" target="_blank" rel="noopener" aria-label="Google Maps" title="Google Maps"
                       class="w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 32" class="h-[18px] w-auto"><defs><clipPath id="pinMaps"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 20 12 20s12-11 12-20C24 5.4 18.6 0 12 0z"/></clipPath></defs><g clip-path="url(#pinMaps)"><rect x="0" y="0" width="12" height="12" fill="#4285f4"/><rect x="12" y="0" width="12" height="12" fill="#ea4335"/><rect x="0" y="12" width="12" height="20" fill="#34a853"/><rect x="12" y="12" width="12" height="20" fill="#fbbc05"/></g><circle cx="12" cy="12" r="4.5" fill="#fff"/></svg>
                    </a>
                </div>
            </div>

            {{-- Alamat & kontak --}}
            <div class="text-xs text-slate-500 space-y-2 md:px-2">
                <div class="flex items-start gap-1.5">
                    <span class="material-icons-outlined text-slate-400 text-sm shrink-0">location_on</span>
                    Jl. Murakata No.4, Barabai, Kab. Hulu Sungai Tengah, Kalsel
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="material-icons-outlined text-slate-400 text-sm shrink-0">phone</span>
                    (0517) 41004
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="material-icons-outlined text-slate-400 text-sm shrink-0">mail</span>
                    rsud.damanhuri@hstkab.go.id
                </div>
            </div>

            {{-- Menu --}}
            <nav class="flex flex-col gap-1.5">
                @foreach ($footerLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="text-xs text-slate-500 hover:text-emerald-700 text-left transition-colors">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Peta Google Maps (pojok kanan) --}}
            <div class="relative rounded-xl overflow-hidden border border-slate-200 shadow-sm">
                <iframe src="{{ $embedMaps }}"
                        title="Lokasi RSUD H. Damanhuri Barabai"
                        class="w-full h-36 block"
                        style="border:0;"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                <a href="{{ $urlMaps }}" target="_blank" rel="noopener"
                   class="absolute top-2 left-2 bg-white text-[10px] font-semibold text-[#1565C0] rounded-md shadow px-2 py-1 inline-flex items-center gap-1 hover:bg-slate-50 transition-colors">
                    Buka di Maps
                    <span class="material-icons-outlined" style="font-size: 12px;">open_in_new</span>
                </a>
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} RSUD H. Damanhuri Barabai &middot; Sistem Informasi Pengaduan Masyarakat
        </div>
    </div>
</footer>
