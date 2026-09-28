@extends('layouts.admin')

@section('title', 'Riwayat Selesai')

@section('content')
<div class="space-y-5">
    <x-admin.judul judul="Riwayat Selesai"
        :sub="$selesai->total() . ' tiket telah diselesaikan oleh unit ini'">
        <x-admin.tombol-ekspor />
    </x-admin.judul>

    {{-- Filter tanggal / bulan / tahun (berdasarkan Tanggal Selesai) --}}
    <form method="GET" action="{{ route('unit.history') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <span class="text-xs font-semibold text-slate-400">Tanggal selesai</span>
        <x-admin.filter-tanggal :tanggal="$f['tanggal']" :bulan="$f['bulan']" :tahun="$f['tahun']" :daftar-tahun="$daftarTahun" />

        @if (\App\Support\PeriodeFilter::aktif($f))
            <a href="{{ route('unit.history') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1">
                <span class="material-icons-outlined" style="font-size: 16px;">close</span> Reset
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Ringkasan Aduan', 'Grading', 'Tanggal Selesai', ''] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($selesai as $t)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-xs font-mono text-navy whitespace-nowrap">{{ $t->nomor_tiket }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs max-w-[260px]">
                                <span class="line-clamp-2 leading-snug">{{ $t->judul }}</span>
                            </td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$t->prioritas" /></td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">
                                {{ $t->selesai_pada?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('unit.tickets.show', ['aduan' => $t->nomor_tiket]) }}"
                                   class="inline-flex items-center gap-1 text-xs text-slate-500 border border-slate-200 font-semibold px-3 py-1.5 rounded-lg hover:border-slate-300 transition">
                                    <span class="material-icons-outlined" style="font-size: 13px;">visibility</span>
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-sm">
                                <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 36px;">history</span>
                                {{ \App\Support\PeriodeFilter::aktif($f) ? 'Tidak ada tiket selesai pada periode ini.' : 'Belum ada tiket yang diselesaikan.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$selesai" />
    </div>
</div>
@endsection
