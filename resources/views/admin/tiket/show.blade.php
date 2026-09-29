@extends('layouts.admin')

@section('title', 'Detail Tiket ' . $aduan->nomor_tiket)
@section('lebar', 'max-w-3xl')

@section('content')
@php
    $status   = $aduan->status instanceof \BackedEnum ? $aduan->status->value : (string) $aduan->status;
    $konfigStatus = [
        'diproses'        => ['label' => 'Diproses',        'bg' => 'bg-amber-100',   'teks' => 'text-amber-700',   'titik' => 'bg-amber-500'],
        'dikoordinasikan' => ['label' => 'Dikoordinasikan', 'bg' => 'bg-blue-100',    'teks' => 'text-blue-700',    'titik' => 'bg-blue-500'],
        'selesai'         => ['label' => 'Selesai',         'bg' => 'bg-emerald-100', 'teks' => 'text-emerald-700', 'titik' => 'bg-emerald-500'],
    ];
    $cfg = $konfigStatus[$status] ?? $konfigStatus['diproses'];

    $namaPelapor = $aduan->anonim ? 'Anonim' : ($aduan->nama_pelapor ?: '-');
    $tglKejadian = $aduan->tanggal_kejadian->locale('id')->translatedFormat('d F Y · H.i');
    $terkirimPada = $aduan->solusi_dikirim_pada?->locale('id')->translatedFormat('d M Y · H.i');

    $langkahAlur = [
        ['n' => 1, 'label' => 'Tiket Dibuat',       'sub' => 'Sistem membuat nomor tiket',          'ikon' => 'inbox'],
        ['n' => 2, 'label' => 'Diteruskan ke Unit', 'sub' => $aduan->unit->nama ?? 'Belum diteruskan', 'ikon' => 'send'],
        ['n' => 3, 'label' => 'Unit Selesaikan',    'sub' => 'Status diperbarui ke Selesai',        'ikon' => 'task_alt'],
        ['n' => 4, 'label' => 'Solusi Dikirim',     'sub' => 'Jawaban dikirim ke WhatsApp pasien',  'ikon' => 'chat'],
    ];

    $grading = $aduan->prioritas;
    $batas   = $aduan->batasWaktu();
    $bolehTeruskan = $bolehUbah && $status !== 'selesai';
    $tampilUbahStatus = $bolehUbah && ! $aduan->solusi_dikirim_pada;
@endphp

<div class="space-y-5 pb-10">

    <a href="{{ route('admin.tickets') }}" class="flex items-center gap-1.5 text-sm text-slate-500 hover:text-navy transition-colors">
        <span class="material-icons-outlined" style="font-size: 16px;">arrow_back</span>
        Kembali ke Semua Tiket
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

        {{-- Grading & batas waktu penanganan --}}
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Grading</span>
                <x-admin.prioritas-badge :prioritas="$grading" />
                @if ($grading)
                    <span class="text-slate-500">{{ $grading->keterangan() }} · {{ $grading->waktuLabel() }}</span>
                @endif
            </div>
            @if ($batas)
                <div class="flex items-center gap-1.5 {{ $aduan->melewatiBatas() ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                    <span class="material-icons-outlined" style="font-size: 14px;">schedule</span>
                    Batas penyelesaian {{ $batas->locale('id')->translatedFormat('d M Y · H.i') }}
                    @if ($aduan->melewatiBatas())
                        · {{ $aduan->selesai_pada ? 'selesai terlambat' : 'melewati batas' }}
                    @elseif ($aduan->selesai_pada)
                        · selesai tepat waktu
                    @endif
                </div>
            @else
                <div class="text-slate-400">Grading ditentukan saat tiket diteruskan ke unit.</div>
            @endif
        </div>

        @if ($bolehTeruskan)
            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-3">
                <button type="button" @click="$dispatch('buka-teruskan')"
                        class="inline-flex items-center gap-2 text-sm font-semibold border border-slate-200 text-slate-600 rounded-xl px-4 py-2 hover:border-navy hover:text-navy transition">
                    <span class="material-icons-outlined" style="font-size: 16px;">forward</span>
                    {{ $aduan->unit ? 'Teruskan Ulang ke Unit Lain' : 'Teruskan ke Unit' }}
                </button>
                @if ($aduan->unit)
                    <div class="flex items-center gap-1.5 text-xs bg-blue-50 border border-blue-100 text-blue-700 rounded-xl px-3 py-1.5">
                        <span class="material-icons-outlined" style="font-size: 14px;">swap_horiz</span>
                        Sudah diteruskan ke <span class="font-bold ml-0.5">{{ $aduan->unit->nama }}</span>
                    </div>
                @endif
            </div>
        @elseif ($aduan->unit)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="inline-flex items-center gap-1.5 text-xs bg-blue-50 border border-blue-100 text-blue-700 rounded-xl px-3 py-1.5">
                    <span class="material-icons-outlined" style="font-size: 14px;">swap_horiz</span>
                    Unit penangan: <span class="font-bold ml-0.5">{{ $aduan->unit->nama }}</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Banner solusi terkirim --}}
    @if ($aduan->solusi_dikirim_pada)
        <div class="flex flex-wrap items-center justify-between gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-5 py-3.5 text-sm text-emerald-700">
            <div class="flex items-center gap-3">
                <span class="material-icons-outlined text-emerald-500" style="font-size: 20px;">check_circle</span>
                <span>
                    @if ($waPelapor)
                        Solusi dikirim ke WhatsApp pasien · <strong>{{ $terkirimPada }}</strong>
                    @else
                        Solusi tersimpan dan tampil di halaman Cek Status · <strong>{{ $terkirimPada }}</strong>
                    @endif
                </span>
            </div>
            @if ($waPelapor && $pesanSolusiTerkirim)
                <a href="{{ $pesanSolusiTerkirim->url() }}" target="_blank" rel="noopener noreferrer"
                   class="shrink-0 text-xs font-bold text-emerald-700 border border-emerald-300 rounded-lg px-3 py-1.5 hover:bg-emerald-100 transition flex items-center gap-1">
                    <span class="material-icons-outlined" style="font-size: 13px;">refresh</span>Buka WhatsApp Lagi
                </a>
            @endif
        </div>
    @endif

    {{-- Data pelapor --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="text-sm font-bold text-navy">Data Pelapor</div>
        <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
            <div>
                <div class="text-xs text-slate-400 mb-0.5">Nama</div>
                <div class="font-semibold text-navy">{{ $namaPelapor }}</div>
            </div>
            <div>
                <div class="text-xs text-slate-400 mb-0.5">Nomor WhatsApp</div>
                @if ($waPelapor)
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold font-mono text-navy">{{ $aduan->no_wa_pelapor }}</span>
                        <a href="https://wa.me/{{ $waPelapor }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1 text-xs text-emerald-700 hover:underline">
                            <span class="material-icons-outlined" style="font-size: 13px;">open_in_new</span>Buka WA
                        </a>
                    </div>
                @else
                    <div class="text-slate-400">—</div>
                @endif
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
                            <div class="text-[10px] leading-tight mt-0.5 max-w-[80px] mx-auto truncate" title="{{ $step['sub'] }}">{{ $step['sub'] }}</div>
                        </div>
                    </div>
                    @if (! $akhir)
                        <div class="flex-1 h-0.5 mt-4 mx-1 transition-all {{ $selesaiLangkah ? 'bg-emerald-400' : 'bg-slate-100' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Langkah 1: belum diteruskan --}}
    @if ($langkah === 1 && $bolehUbah)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <span class="material-icons-outlined text-amber-600" style="font-size: 20px;">forward</span>
            </div>
            <div>
                <div class="text-sm font-bold text-amber-800 mb-1">Teruskan tiket ke unit terlebih dahulu</div>
                <div class="text-sm text-amber-700">Tentukan grading aduan (Merah, Kuning, atau Hijau), lalu teruskan ke unit/poli terkait via WhatsApp sebelum solusi dapat dikirim ke pelapor.</div>
                <button type="button" @click="$dispatch('buka-teruskan')"
                        class="mt-3 inline-flex items-center gap-2 text-sm font-semibold bg-amber-600 text-white rounded-xl px-4 py-2 hover:bg-amber-700 transition">
                    <span class="material-icons-outlined" style="font-size: 16px;">send</span>
                    Teruskan ke Unit Sekarang
                </button>
            </div>
        </div>
    @endif

    {{-- Langkah 2: menunggu unit --}}
    @if ($langkah === 2)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                <span class="material-icons-outlined text-blue-600" style="font-size: 20px;">hourglass_top</span>
            </div>
            <div>
                <div class="text-sm font-bold text-blue-800 mb-1">Menunggu unit menyelesaikan pengaduan</div>
                <div class="text-sm text-blue-700">
                    Tiket sudah diteruskan ke <span class="font-bold">{{ $aduan->unit->nama ?? '-' }}</span>.
                    Formulir solusi akan terbuka setelah status menjadi <span class="font-bold">Selesai</span>.
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs text-blue-600">
                    <span class="material-icons-outlined" style="font-size: 14px;">info</span>
                    Status saat ini:
                    <span class="font-bold px-2 py-0.5 rounded-full ml-1 {{ $cfg['bg'] }} {{ $cfg['teks'] }}">{{ $cfg['label'] }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- Perbarui status (Super Admin & Operator) --}}
    @if ($tampilUbahStatus)
        <form method="POST" action="{{ route('admin.tickets.status', ['aduan' => $aduan->nomor_tiket]) }}"
              class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            @csrf
            <div class="text-sm font-bold text-navy">Perbarui Status &amp; Catatan</div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-navy">Status Tiket</label>
                <select name="status" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition bg-white">
                    @foreach ($konfigStatus as $kode => $k)
                        <option value="{{ $kode }}" @selected($status === $kode)>{{ $k['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 text-navy">Catatan (opsional)</label>
                <textarea name="catatan" rows="3" placeholder="Tuliskan catatan tindakan atau tanggapan…"
                          class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-navy resize-none transition">{{ old('catatan') }}</textarea>
            </div>
            <button type="submit" class="w-full grad-btn text-white font-semibold py-3 rounded-xl transition">Simpan Perubahan</button>
        </form>
    @endif

    {{-- Langkah 3 & 4: solusi --}}
    @if ($langkah >= 3 && ($bolehUbah || $aduan->solusi))
        <form method="POST" action="{{ route('admin.tickets.solution', ['aduan' => $aduan->nomor_tiket]) }}"
              class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-5"
              x-data="{
                  solusi: @js(old('solusi', $aduan->solusi ?? ($jawabanUnit['teks'] ?? ''))),
                  awal: @js($jawabanUnit['teks'] ?? null),
                  templat: @js($templatSolusi),
                  wa: @js($waPelapor),
                  terkunci: @js((bool) $aduan->solusi_dikirim_pada),
                  get pratinjau() { return this.templat.replace('__SOLUSI__', () => this.solusi); },
                  get siap() { return this.solusi.trim().length >= 5; },
                  pakaiJawabanUnit() { this.solusi = this.awal; }
              }"
              @submit="if (wa && !terkunci) { window.open('https://wa.me/' + wa + '?text=' + encodeURIComponent(pratinjau), '_blank'); }">
            @csrf
            <div class="flex items-center gap-2">
                <span class="material-icons-outlined text-emerald-600" style="font-size: 20px;">chat</span>
                <div class="text-sm font-bold text-navy">Kirim Solusi / Jawaban ke Pelapor</div>
                <div class="ml-auto flex items-center gap-1 text-xs text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full px-2.5 py-1">
                    <span class="material-icons-outlined" style="font-size: 13px;">task_alt</span>
                    Tiket berstatus Selesai
                </div>
            </div>

            @if ($aduan->solusi_dikirim_pada)
                <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap border border-slate-100">{{ $aduan->solusi }}</div>
            @else
                @if ($jawabanUnit)
                    {{-- Jawaban petugas unit dimasukkan otomatis; admin bebas menambah atau mengubahnya. --}}
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-xs text-blue-700">
                        <span class="material-icons-outlined shrink-0" style="font-size: 18px;">auto_fix_high</span>
                        <div class="flex-1 leading-relaxed">
                            <div class="font-semibold">Jawaban dari {{ $jawabanUnit['unit'] ?? 'unit' }} dimasukkan otomatis</div>
                            <div class="text-blue-600">Ditulis oleh {{ $jawabanUnit['petugas'] }}. Anda bisa menambahkan atau mengubahnya sebelum dikirim ke pelapor.</div>
                        </div>
                        <button type="button" x-show="solusi !== awal" x-cloak @click="pakaiJawabanUnit()"
                                class="shrink-0 font-semibold underline hover:text-blue-900">Kembalikan jawaban unit</button>
                    </div>
                @endif
                <textarea name="solusi" x-model="solusi" rows="6"
                          placeholder="Tuliskan solusi atau penjelasan penanganan untuk disampaikan kepada pelapor…"
                          class="w-full border border-slate-200 rounded-xl px-4 py-3.5 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-emerald-500 resize-y transition leading-relaxed">{{ old('solusi', $aduan->solusi ?? ($jawabanUnit['teks'] ?? '')) }}</textarea>
            @endif

            @if ($waPelapor)
                <div x-show="solusi.trim() !== ''" x-cloak>
                    <div class="text-xs text-slate-400 uppercase tracking-wider mb-2">Pratinjau Pesan WhatsApp ke Pasien</div>
                    <div class="bg-[#e5ddd5] rounded-2xl p-4">
                        <div class="max-w-xs ml-auto">
                            <div class="bg-white rounded-2xl rounded-tr-sm shadow-sm px-4 py-3 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap" x-text="pratinjau"></div>
                            <div class="text-right text-[10px] text-slate-400 mt-1 flex items-center justify-end gap-1">
                                {{ ($aduan->solusi_dikirim_pada ?? now())->format('H.i') }}
                                @if ($aduan->solusi_dikirim_pada)
                                    <span class="material-icons-outlined text-blue-500" style="font-size: 13px;">done_all</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (! $aduan->solusi_dikirim_pada)
                <div class="flex items-start gap-2 text-xs text-slate-500 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                    <span class="material-icons-outlined text-slate-400" style="font-size: 16px;">info</span>
                    Pelapor {{ $aduan->anonim ? 'memilih anonim' : 'tidak mengisi nomor WhatsApp' }}. Solusi akan disimpan dan tampil di halaman Cek Status.
                </div>
            @endif

            @if (! $aduan->solusi_dikirim_pada && $bolehUbah)
                <button type="submit" :disabled="!siap"
                        :class="siap ? 'bg-emerald-600 hover:bg-emerald-700 shadow-md text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                        class="w-full flex items-center justify-center gap-2.5 py-4 rounded-xl font-bold text-base transition-all">
                    <span class="material-icons-outlined" style="font-size: 20px;">chat</span>
                    {{ $waPelapor ? 'Kirim Solusi ke WhatsApp Pasien' : 'Simpan Solusi' }}
                </button>
            @elseif ($aduan->solusi_dikirim_pada)
                <div class="flex items-center gap-2 text-xs text-slate-400 border-t border-slate-100 pt-4">
                    <span class="material-icons-outlined" style="font-size: 14px;">lock</span>
                    Solusi telah dikirim pada {{ $terkirimPada }} — tidak dapat diubah kembali.
                </div>
            @endif
        </form>
    @endif

    {{-- Jendela: Teruskan ke Unit --}}
    @if ($bolehTeruskan)
        <div x-data="{
                buka: false, unitId: '',
                grading: @js($aduan->prioritas?->value ?? ''),
                units: @js($units),
                opsi: @js($opsiGrading),
                warna: {
                    tinggi: 'border-red-500 bg-red-50 text-red-700',
                    sedang: 'border-amber-500 bg-amber-50 text-amber-700',
                    rendah: 'border-emerald-500 bg-emerald-50 text-emerald-700'
                },
                pesan: @js($pesanDisposisi),
                get unit() { return this.units.find(u => u.id == this.unitId) || null; },
                get gradingDipilih() { return this.opsi.find(o => o.nilai === this.grading) || null; },
                get pesanFinal() {
                    const g = this.gradingDipilih;
                    return this.pesan
                        .replace('__GRADING__', () => g ? g.label + ' (' + g.waktu + ')' : '(pilih grading)')
                        .replace('__BATAS__', () => g ? g.batas : '-');
                },
                get siap() { return !!this.unitId && !!this.grading; },
                kirim() {
                    if (!this.siap) return;
                    window.open('https://wa.me/' + ((this.unit && this.unit.wa) || '') + '?text=' + encodeURIComponent(this.pesanFinal), '_blank');
                    this.$refs.form.submit();
                }
             }"
             @buka-teruskan.window="buka = true" @keydown.escape.window="buka = false" x-cloak>
            <div x-show="buka" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 style="background-color: rgba(15,46,90,0.25); backdrop-filter: blur(2px);" @click.self="buka = false">
                <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5 max-h-[calc(100vh-2rem)] overflow-y-auto" style="animation: fadeIn .2s ease;">
                    <div class="flex items-center justify-between">
                        <div class="text-base font-bold text-navy">Teruskan ke Unit</div>
                        <button type="button" @click="buka = false" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup">
                            <span class="material-icons-outlined" style="font-size: 20px;">close</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Unit Tujuan</label>
                        <x-admin.unit-picker :units="$units" x-model="unitId" placeholder="Cari unit tujuan…" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Grading Aduan</label>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="o in opsi" :key="o.nilai">
                                <button type="button" @click="grading = o.nilai"
                                        :class="grading === o.nilai ? warna[o.nilai] : 'border-slate-200 text-slate-600 hover:border-slate-300'"
                                        class="border-2 rounded-xl px-2 py-2.5 text-center transition">
                                    <div class="text-sm font-bold" x-text="o.label"></div>
                                    <div class="text-[10px] leading-tight" x-text="o.keterangan"></div>
                                    <div class="text-[10px] font-semibold mt-0.5" x-text="o.waktu"></div>
                                </button>
                            </template>
                        </div>
                        <div class="mt-2 text-xs text-slate-500" x-show="gradingDipilih" x-cloak>
                            Batas penyelesaian: <span class="font-semibold text-navy" x-text="gradingDipilih && gradingDipilih.batas"></span>
                            <span class="text-slate-400">(sejak aduan diterima)</span>
                        </div>
                    </div>

                    <div x-show="unitId" x-cloak>
                        <div class="text-xs text-slate-400 uppercase tracking-wider mb-2">Pratinjau Pesan WA ke Unit</div>
                        <div class="bg-[#e5ddd5] rounded-2xl p-4">
                            <div class="max-w-sm ml-auto">
                                <div class="bg-white rounded-2xl rounded-tr-sm shadow-sm px-4 py-3 text-slate-700 leading-relaxed whitespace-pre-wrap font-mono text-xs" x-text="pesanFinal"></div>
                            </div>
                        </div>
                        <div class="mt-2 text-xs" :class="unit && unit.wa ? 'text-slate-500' : 'text-amber-600'"
                             x-text="unit && unit.wa ? ('Dikirim ke WhatsApp unit: +' + unit.wa) : 'Nomor WhatsApp unit belum diisi. WhatsApp akan meminta Anda memilih kontak.'"></div>
                    </div>

                    <form x-ref="form" method="POST" action="{{ route('admin.tickets.forward', ['aduan' => $aduan->nomor_tiket]) }}">
                        @csrf
                        <input type="hidden" name="unit_id" :value="unitId">
                        <input type="hidden" name="prioritas" :value="grading">
                    </form>

                    <div class="flex gap-3 pt-1">
                        <button type="button" @click="buka = false"
                                class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:border-slate-300 transition">Batal</button>
                        <button type="button" @click="kirim()" :disabled="!siap"
                                class="flex-1 grad-btn text-white text-sm font-semibold py-2.5 rounded-xl disabled:opacity-40 transition flex items-center justify-center gap-1.5">
                            <span class="material-icons-outlined" style="font-size: 15px;">chat</span>Kirim ke Unit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
