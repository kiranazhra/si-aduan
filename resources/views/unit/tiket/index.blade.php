@extends('layouts.admin')

@section('title', 'Tiket Unit Saya')

@section('content')
<div class="space-y-5">
    <x-admin.judul judul="Tiket Unit Saya"
        :sub="$tiket->total() . ' dari ' . $totalSemua . ' tiket ditampilkan'">
        <x-admin.tombol-ekspor />
    </x-admin.judul>

    {{-- Filter --}}
    <form method="GET" action="{{ route('unit.tickets') }}"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex flex-wrap gap-3">
        <div class="flex items-center gap-2 flex-1 min-w-40">
            <span class="material-icons-outlined text-slate-400" style="font-size: 20px;">search</span>
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari no. tiket atau judul..."
                   class="flex-1 text-sm focus:outline-none text-slate-700 placeholder:text-slate-400">
        </div>

        <select name="status" onchange="this.form.submit()"
                class="border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-navy transition bg-white">
            <option value="">Semua Status</option>
            @foreach (\App\Enums\StatusAduan::cases() as $s)
                <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>

        {{-- Filter tanggal / bulan / tahun (berdasarkan Tanggal Masuk) --}}
        <x-admin.filter-tanggal :tanggal="$f['tanggal']" :bulan="$f['bulan']" :tahun="$f['tahun']" :daftar-tahun="$daftarTahun" />

        <button type="submit" class="grad-btn text-white text-sm font-semibold rounded-xl px-4 py-2 transition">Cari</button>

        @if ($q !== '' || $status !== '' || \App\Support\PeriodeFilter::aktif($f))
            <a href="{{ route('unit.tickets') }}" class="text-sm text-slate-500 hover:text-slate-700 flex items-center gap-1 self-center">
                <span class="material-icons-outlined" style="font-size: 18px;">close</span> Reset
            </a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach (['No. Tiket', 'Ringkasan Aduan', 'Grading', 'Tanggal Masuk', 'Status', 'Aksi'] as $h)
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-400 whitespace-nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiket as $t)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-bold text-xs font-mono text-navy whitespace-nowrap">{{ $t->nomor_tiket }}</td>
                            <td class="py-3 px-4 text-slate-600 text-xs max-w-[240px]">
                                <span class="line-clamp-2 leading-snug">{{ $t->judul }}</span>
                            </td>
                            <td class="py-3 px-4"><x-admin.prioritas-badge :prioritas="$t->prioritas" /></td>
                            <td class="py-3 px-4 text-slate-500 text-xs whitespace-nowrap">{{ $t->dibuat_pada?->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-3 px-4"><x-admin.status-badge :status="$t->status" /></td>
                            <td class="py-3 px-4">
                                @if ($t->status !== \App\Enums\StatusAduan::Selesai)
                                    <a href="{{ route('unit.tickets.show', ['aduan' => $t->nomor_tiket]) }}"
                                       class="inline-flex items-center gap-1 text-xs text-white grad-btn font-semibold px-3 py-1.5 rounded-lg hover:opacity-90 transition">
                                        <span class="material-icons-outlined" style="font-size: 15px;">reply</span>
                                        Tanggapi
                                    </a>
                                @else
                                    <a href="{{ route('unit.tickets.show', ['aduan' => $t->nomor_tiket]) }}"
                                       class="inline-flex items-center gap-1 text-xs text-slate-500 border border-slate-200 font-semibold px-3 py-1.5 rounded-lg hover:border-slate-300 transition">
                                        <span class="material-icons-outlined" style="font-size: 15px;">visibility</span>
                                        Lihat
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 text-sm">
                                <span class="material-icons-outlined block mb-2 text-slate-200" style="font-size: 38px;">search_off</span>
                                Tidak ada tiket yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$tiket" />
    </div>
</div>
@endsection
