{{-- Jendela "Detail" daftar tiket. Buka dengan: $dispatch('buka-detail', { judul: '...', url: '...' }) --}}
<div x-data="detailTiket()" @buka-detail.window="muat($event.detail)" @keydown.escape.window="buka = false" x-cloak>
    <div x-show="buka" class="no-print fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(15,46,90,0.3); backdrop-filter: blur(3px);" @click.self="buka = false">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-4xl max-h-[85vh] flex flex-col" style="animation: fadeIn .2s ease;">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div>
                    <div class="text-base font-bold text-navy" x-text="judul"></div>
                    <div class="text-xs text-slate-400 mt-0.5" x-text="memuat ? 'Memuat…' : (jumlah + ' tiket')"></div>
                </div>
                <button type="button" @click="buka = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup">
                    <span class="material-icons-outlined" style="font-size: 24px;">close</span>
                </button>
            </div>
            <div class="overflow-auto flex-1" x-html="html"></div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', function () {
                Alpine.data('detailTiket', function () {
                    return {
                        buka: false, judul: '', jumlah: 0, html: '', memuat: false,
                        async muat(d) {
                            this.buka = true; this.judul = d.judul; this.html = ''; this.jumlah = 0; this.memuat = true;
                            try {
                                const r = await fetch(d.url, { headers: { 'Accept': 'application/json' } });
                                const j = await r.json();
                                this.jumlah = j.jumlah; this.html = j.html;
                            } catch (e) {
                                this.html = '<div class="p-8 text-center text-sm text-red-500">Gagal memuat data.</div>';
                            }
                            this.memuat = false;
                        }
                    };
                });
            });
        </script>
    @endpush
@endonce
