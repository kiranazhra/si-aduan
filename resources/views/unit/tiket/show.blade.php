@extends('layouts.admin')

@section('title', 'Detail Tiket ' . $aduan->nomor_tiket)
@section('lebar', 'max-w-3xl')

@section('content')
@php
    $status = $aduan->status instanceof \BackedEnum ? $aduan->status->value : (string) $aduan->status;
    $konfigStatus = [
        'diproses'        => ['label' => 'Diproses',        'bg' => 'bg-amber-100',   'teks' => 'text-amber-700',   'titik' => 'bg-amber-500'],
        'dikoordinasikan' => ['label' => 'Dikoordinasikan', 'bg' => 'bg-blue-100',    'teks' => 'text-blue-700',    'titik' => 'bg-blue-500'],
        'selesai'         => ['label' => 'Selesai',         'bg' => 'bg-emerald-100', 'teks' => 'text-emerald-700', 'titik' => 'bg-emerald-500'],
    ];
    $cfg = $konfigStatus[$status] ?? $konfigStatus['diproses'];

    $namaPelapor  = $aduan->anonim ? 'Anonim' : ($aduan->nama_pelapor ?: '-');
    $tglKejadian  = $aduan->tanggal_kejadian->locale('id')->translatedFormat('d F Y · H.i');
    $terkirimPada = $aduan->solusi_dikirim_pada?->locale('id')->translatedFormat('d M Y · H.i');

    $langkahAlur = [
        ['n' => 1, 'label' => 'Diteruskan Admin', 'sub' => 'Aduan didisposisikan ke unit', 'ikon' => 'send'],
        ['n' => 2, 'label' => 'Ditangani Unit',   'sub' => 'Petugas unit menindaklanjuti', 'ikon' => 'manage_accounts'],
        ['n' => 3, 'label' => 'Selesai',          'sub' => 'Status diperbarui ke Selesai', 'ikon' => 'task_alt'],
        ['n' => 4, 'label' => 'Solusi Dikirim',   'sub' => 'Dikirim Admin ke pelapor',     'ikon' => 'chat'],
    ];

    $bolehUbah = ! $aduan->solusi_dikirim_pada;
@endphp

<div class="space-y-5 pb-10">

    <a href="{{ route('unit.tickets') }}" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-navy transition-colors">
        <span class="material-icons-outlined" style="font-size: 16px;">arrow_back</span>
        Kembali ke Tiket Unit Saya
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="text-xs text-slate-400 uppercase tracking-wider mb-1">Nomor Tiket</div>
                <div class="text-2xl font-mono font-extrabold tracking-wide text-navy">{{ $aduan->nomor_tiket }}</div>
                <div class="flex flex-wrap items-center gap-3 mt-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1"><span class="material-icons-outlined" style="font-size: 14px;">category</span>{{ $aduan->kategori->nama ?? '-' }}</span>
                    <span class="flex items-center gap-1"><span class="material-icons-outlined" style="font-size: 14px;">calendar_today</span>{{ $tglKejadian }}</span>
                    <span class="flex items-center gap-1"><span class="material-icons-outlined" style="font-size: 14px;">apartment</span>{{ $aduan->lokasi->nama ?? '-' }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3.5 py-2 rounded-full text-sm font-bold {{ $cfg['bg'] }} {{ $cfg['teks'] }}">
                <span class="w-2 h-2 rounded-full {{ $cfg['titik'] }}"></span>{{ $cfg['label'] }}
            </div>
        </div>
    </div>

    {{-- Banner solusi terkirim --}}
    @if ($aduan->solusi_dikirim_pada)
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3.5 text-sm text-emerald-700">
            <span class="material-icons-outlined text-emerald-500" style="font-size: 20px;">check_circle</span>
            <span>Solusi untuk tiket ini sudah dikirim Admin ke pelapor pada <strong>{{ $terkirimPada }}</strong>. Status tidak dapat diubah lagi.</span>
        </div>
    @endif

    {{-- Data pengaduan --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="text-sm font-bold text-navy">Data Pelapor &amp; Pengaduan</div>
        <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div>
                <div class="text-xs text-slate-400 mb-0.5">Pelapor</div>
                <div class="font-semibold text-navy">{{ $namaPelapor }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-0.5">Grading</div>
                <x-admin.prioritas-badge :prioritas="$aduan->prioritas" />
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-0.5">Penilaian Pelayanan</div>
                @if ($aduan->rating)
                    <div class="flex items-center gap-2">
                        <x-admin.bintang :nilai="$aduan->rating" ukuran="w-4 h-4" />
                        <span class="text-xs font-semibold text-navy">{{ $aduan->rating }}/5 · {{ $aduan->labelRating() }}</span>
                    </div>
                @else
                    <div class="text-slate-400">—</div>
                @endif
            </div>
        </div>

        <div x-data="{ terbuka: false }">
            <div class="text-xs text-slate-400 mb-1.5">Isi Pengaduan</div>
            <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-700 leading-relaxed">
                <div class="font-semibold text-navy mb-1">{{ $aduan->judul }}</div>
                @php $panjang = mb_strlen($aduan->uraian) > 160; @endphp
                <span class="whitespace-pre-line" x-show="!terbuka">{{ $panjang ? mb_substr($aduan->uraian, 0, 160) . '…' : $aduan->uraian }}</span>
                @if ($panjang)
                    <span class="whitespace-pre-line" x-show="terbuka" x-cloak>{{ $aduan->uraian }}</span>
                    <button type="button" @click="terbuka = !terbuka" class="ml-1 text-emerald-700 text-xs font-semibold hover:underline"
                            x-text="terbuka ? 'Tutup' : 'Selengkapnya'"></button>
                @endif
            </div>
        </div>

        <div x-data="{ pratinjau: null }">
            <div class="text-xs text-slate-400 mb-2">Lampiran Bukti</div>
            @if ($aduan->lampiran->isEmpty())
                <div class="text-xs text-slate-400">Tidak ada lampiran.</div>
            @else
                <div class="flex flex-wrap gap-3">
                    @foreach ($aduan->lampiran as $l)
                        @if ($l->adalahGambar())
                            <button type="button" @click="pratinjau = '{{ $l->url() }}'"
                                    class="flex items-center gap-2 border border-slate-200 rounded-xl pl-1.5 pr-3 py-1.5 text-xs text-slate-600 hover:border-navy transition">
                                <img src="{{ $l->url() }}" alt="{{ $l->nama_file }}" class="w-8 h-8 rounded-lg object-cover">
                                {{ $l->nama_file }}
                            </button>
                        @else
                            <a href="{{ $l->url() }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-2 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-600 hover:border-navy transition">
                                <span class="material-icons-outlined text-slate-400" style="font-size: 16px;">description</span>
                                {{ $l->nama_file }}
                            </a>
                        @endif
                    @endforeach
                </div>

                {{-- Pratinjau gambar --}}
                <div x-show="pratinjau" x-cloak x-transition.opacity
                     class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-6"
                     @click="pratinjau = null" @keydown.escape.window="pratinjau = null">
                    <button type="button" @click="pratinjau = null"
                            class="absolute top-5 right-5 text-white/80 hover:text-white">
                        <span class="material-icons-outlined" style="font-size: 28px;">close</span>
                    </button>
                    <img :src="pratinjau" @click.stop class="max-w-full max-h-full rounded-xl shadow-2xl">
                </div>
            @endif
        </div>
    </div>

    {{-- Riwayat penanganan --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="text-sm font-bold mb-5 text-navy">Riwayat Penanganan</div>
        <div class="relative pl-8">
            @foreach ($aduan->riwayat as $r)
                @php
                    $terakhir = $loop->last;
                    $khusus   = in_array($r->jenis, [\App\Enums\JenisRiwayat::SolusiDikirim, \App\Enums\JenisRiwayat::Selesai], true);
                    $ikon     = $r->jenis === \App\Enums\JenisRiwayat::SolusiDikirim ? 'check_circle' : $r->jenis->ikon();
                @endphp
                <div class="relative pb-5 last:pb-0">
                    @if (! $terakhir)
                        <div class="absolute left-[-1.25rem] top-5 bottom-0 w-px bg-slate-100"></div>
                    @endif
                    <div class="absolute left-[-1.75rem] top-0.5 w-6 h-6 rounded-full flex items-center justify-center {{ $khusus ? 'bg-emerald-50 border border-emerald-200' : 'bg-white border border-slate-200' }}">
                        <span class="material-icons-outlined {{ $khusus ? 'text-emerald-500' : 'text-slate-400' }}" style="font-size: 13px;">{{ $ikon }}</span>
                    </div>
                    <div class="text-sm font-medium text-slate-700">{{ $r->judul }}</div>
                    @if ($r->catatan)
                        <div class="text-xs text-slate-500 mt-0.5 whitespace-pre-line">“{{ $r->catatan }}”</div>
                    @endif
                    <div class="text-xs text-slate-400 mt-0.5">
                        {{ $r->dibuat_pada?->locale('id')->translatedFormat('d M Y · H.i') }}
                        @if ($r->petugas) · {{ $r->petugas->name }} @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Alur penanganan --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-5">Alur Penanganan Aduan</div>
        <div class="flex items-start gap-0">
            @foreach ($langkahAlur as $step)
                @php
                    $selesaiLangkah = $langkah > $step['n'];
                    $aktifLangkah   = $langkah === $step['n'];
                    $akhir          = $loop->last;
                @endphp
                <div class="flex items-start flex-1">
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border-2 transition-all {{ $selesaiLangkah ? 'bg-emerald-500 border-emerald-500 text-white' : ($aktifLangkah ? 'bg-white border-[#1565C0] text-[#1565C0]' : 'bg-white border-slate-200 text-slate-300') }}">
                            <span class="material-icons-outlined" style="font-size: 17px;">{{ $selesaiLangkah ? 'check' : $step['ikon'] }}</span>
                        </div>
                        <div class="mt-2 text-center px-1 {{ $aktifLangkah ? 'text-navy' : ($selesaiLangkah ? 'text-slate-500' : 'text-slate-300') }}">
                            <div class="text-xs leading-tight {{ $aktifLangkah ? 'font-bold' : 'font-medium' }}">{{ $step['label'] }}</div>
                            <div class="text-[10px] leading-tight mt-0.5 max-w-[84px] mx-auto truncate" title="{{ $step['sub'] }}">{{ $step['sub'] }}</div>
                        </div>
                    </div>
                    @if (! $akhir)
                        <div class="flex-1 h-0.5 mt-4 mx-1 transition-all {{ $selesaiLangkah ? 'bg-emerald-400' : 'bg-slate-100' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Form tanggapan / ubah status --}}
    @if ($bolehUbah)
        <form method="POST" action="{{ route('unit.tickets.status', ['aduan' => $aduan->nomor_tiket]) }}"
              class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            @csrf
            <div class="flex items-center gap-2">
                <span class="material-icons-outlined text-emerald-600" style="font-size: 20px;">reply</span>
                <div class="text-sm font-bold text-navy">Tanggapi &amp; Perbarui Status</div>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-navy">Status Tiket</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition bg-white">
                    @foreach ($konfigStatus as $kode => $k)
                        <option value="{{ $kode }}" @selected($status === $kode)>{{ $k['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-navy">Catatan Tanggapan (opsional)</label>
                <textarea name="catatan" rows="4" placeholder="Tuliskan tindakan atau tanggapan unit atas pengaduan ini…"
                          class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-navy resize-none transition">{{ old('catatan') }}</textarea>
            </div>
            <button type="submit" class="w-full grad-btn text-white font-semibold py-3 rounded-xl transition">Simpan Tanggapan</button>
            <p class="text-xs text-slate-400 flex items-start gap-1.5">
                <span class="material-icons-outlined shrink-0" style="font-size: 14px;">info</span>
                Solusi akhir ke pelapor dikirim oleh Admin melalui WhatsApp setelah status tiket ini Selesai.
            </p>
        </form>
    @endif
</div>
@endsection
