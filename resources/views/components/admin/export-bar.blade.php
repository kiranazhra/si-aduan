{{-- Kotak cari + tombol Excel (CSV) & PDF (cetak), seperti ExportBar di prototype --}}
@props(['count', 'total', 'q' => '', 'placeholder' => 'Cari…'])
<form method="GET" class="no-print flex flex-wrap items-center justify-between gap-3">
    @foreach (request()->except(['q', 'page', 'export', 'cetak']) as $k => $v)
        @if (is_scalar($v))
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach

    <div class="flex items-center gap-2 border border-slate-200 rounded-xl px-4 py-2.5 bg-white flex-1 min-w-52">
        <span class="material-icons-outlined text-slate-400" style="font-size: 20px;">search</span>
        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $placeholder }}"
               class="flex-1 text-sm focus:outline-none text-slate-700 placeholder:text-slate-400">
        @if ($q !== '')
            <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}" class="text-slate-400 hover:text-slate-600 transition" title="Hapus pencarian">
                <span class="material-icons-outlined" style="font-size: 18px;">close</span>
            </a>
        @endif
    </div>

    <div class="flex items-center gap-2 shrink-0">
        <span class="text-xs text-slate-400 mr-1">{{ $count }} dari {{ $total }} baris</span>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}"
           class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-xl px-3.5 py-2.5 hover:bg-emerald-50 transition">
            <span class="material-icons-outlined" style="font-size: 17px;">table_view</span>
            Excel
        </a>
        <button type="button" onclick="window.print()"
                class="flex items-center gap-1.5 text-xs font-semibold text-red-600 border border-red-200 rounded-xl px-3.5 py-2.5 hover:bg-red-50 transition">
            <span class="material-icons-outlined" style="font-size: 17px;">picture_as_pdf</span>
            PDF
        </button>
    </div>
</form>
