{{-- Isi jendela "Detail" (dimuat lewat fetch). Hanya tabel, tanpa layout. --}}
<table class="w-full text-sm">
    <thead class="bg-slate-50 border-b border-slate-100 sticky top-0">
        <tr>
            @foreach (array_merge(['No. Tiket', 'Pelapor', 'Tanggal', 'Ringkasan', 'Grading', 'Status'], ($tampilRating ?? false) ? ['Penilaian'] : []) as $h)
                <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($rows as $r)
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                <td class="py-3 px-4 font-bold text-xs font-mono whitespace-nowrap">
                    <a href="{{ route('admin.tickets.show', ['aduan' => $r->nomor_tiket]) }}" class="text-navy hover:underline">{{ $r->nomor_tiket }}</a>
                </td>
                <td class="py-3 px-4 text-slate-600 text-xs whitespace-nowrap">{{ $r->anonim ? 'Anonim' : $r->nama_pelapor }}</td>
                <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $r->dibuat_pada->locale('id')->translatedFormat('d M Y') }}</td>
                <td class="py-3 px-4 text-slate-600 text-xs max-w-[240px]"><span class="line-clamp-2 leading-snug">{{ $r->judul }}</span></td>
                <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$r->prioritas" /></td>
                <td class="py-3 px-4"><x-admin.status-badge :status="$r->status" /></td>
                @if ($tampilRating ?? false)
                    <td class="py-3 px-4 whitespace-nowrap">
                        @if ($r->rating)
                            <x-admin.bintang :nilai="$r->rating" ukuran="w-3.5 h-3.5" />
                            <span class="text-xs text-slate-500 ml-1">{{ $r->labelRating() }}</span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr><td colspan="{{ ($tampilRating ?? false) ? 7 : 6 }}" class="py-10 text-center text-slate-400 text-sm">Tidak ada data.</td></tr>
        @endforelse
    </tbody>
</table>
@if ($jumlah > $rows->count())
    <div class="px-6 py-3 text-xs text-slate-400 border-t border-slate-100">Menampilkan {{ $rows->count() }} tiket terbaru dari {{ $jumlah }} tiket. Gunakan tombol Excel untuk data lengkap.</div>
@endif
