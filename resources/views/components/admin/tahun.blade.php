{{-- Pilihan tahun untuk halaman rekap --}}
@props(['tahun', 'daftar'])
<form method="GET" class="no-print flex items-center gap-2">
    @foreach (request()->except(['tahun', 'page', 'export', 'cetak']) as $k => $v)
        @if (is_scalar($v))
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endif
    @endforeach
    <label class="text-xs text-slate-400" for="pilih-tahun">Tahun</label>
    <select id="pilih-tahun" name="tahun" onchange="this.form.submit()"
            class="border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white focus:outline-none focus:border-navy transition">
        @foreach ($daftar as $t)
            <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
        @endforeach
    </select>
</form>
