{{-- Tombol unduh Excel (.xlsx) dan PDF (halaman cetak berisi tabel saja) sesuai filter yang sedang aktif. --}}
<div class="no-print flex items-center gap-2">
    <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx', 'cetak' => null, 'page' => null]) }}"
       class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-xl px-3.5 py-2.5 hover:bg-emerald-50 transition bg-white">
        <span class="material-icons-outlined" style="font-size: 15px;">table_view</span>
        Excel
    </a>
    <a href="{{ request()->fullUrlWithQuery(['cetak' => 1, 'export' => null, 'page' => null]) }}" target="_blank" rel="noopener"
       class="flex items-center gap-1.5 text-xs font-semibold text-red-600 border border-red-200 rounded-xl px-3.5 py-2.5 hover:bg-red-50 transition bg-white">
        <span class="material-icons-outlined" style="font-size: 15px;">picture_as_pdf</span>
        PDF
    </a>
</div>
