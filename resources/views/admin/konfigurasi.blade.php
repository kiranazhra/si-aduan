@extends('layouts.admin')

@section('title', 'Konfigurasi')

@section('content')
@php
    $lencanaPeran = [
        'super_admin'  => 'bg-purple-100 text-purple-700',
        'operator'     => 'bg-blue-100 text-blue-700',
        'petugas_unit' => 'bg-emerald-100 text-emerald-700',
        'viewer'       => 'bg-slate-100 text-slate-600',
    ];

    $deskripsiPeran = collect($peran)->mapWithKeys(fn ($p) => [$p->value => $p->deskripsi()])->all();

    $pengaturanLabel = [
        'notifikasi_otomatis'  => 'Notifikasi WhatsApp otomatis ke pelapor saat status berubah',
        'tutup_otomatis'       => 'Tutup tiket otomatis setelah ' . $hariTutup . ' hari tanpa respons',
        'peringatan_prioritas' => 'Kirim peringatan WhatsApp ke admin untuk tiket grading Merah',
    ];

    // Data awal untuk jendela isian (dibuka kembali otomatis bila ada galat validasi)
    $modalLama = old('_modal');
    $awal = [
        'modal'  => $modalLama,
        'diriId' => auth()->id(),
        'deskripsi' => $deskripsiPeran,
        'unit'   => $modalLama === 'unit' ? [
            'id' => old('_id') ?: null, 'kode' => old('kode', ''), 'nama' => old('nama', ''),
            'kelompok' => old('kelompok', 'poli'), 'penanggung_jawab' => old('penanggung_jawab', ''),
            'no_wa' => old('no_wa', ''), 'aktif' => (bool) old('aktif', true),
        ] : ['id' => null, 'kode' => '', 'nama' => '', 'kelompok' => 'poli', 'penanggung_jawab' => '', 'no_wa' => '', 'aktif' => true],
        'staf'   => $modalLama === 'staf' ? [
            'id' => old('_id') ?: null, 'name' => old('name', ''), 'email' => old('email', ''), 'password' => '',
            'peran' => old('peran', 'petugas_unit'), 'unit_id' => old('unit_id', ''),
            'no_wa' => old('no_wa', ''), 'aktif' => (bool) old('aktif', true),
        ] : ['id' => null, 'name' => '', 'email' => '', 'password' => '', 'peran' => 'petugas_unit', 'unit_id' => '', 'no_wa' => '', 'aktif' => true],
    ];
@endphp

<div class="space-y-6" x-data="konfigurasi(@js($awal))">

    <x-admin.judul judul="Konfigurasi" sub="Kelola unit pelayanan, akses staf, dan pengaturan sistem." />

    @unless ($bolehUbah)
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 text-slate-500 text-xs rounded-xl px-4 py-3">
            <span class="material-icons-outlined" style="font-size: 16px;">lock</span>
            Hanya Super Admin yang dapat mengubah konfigurasi. Anda dapat melihat datanya saja.
        </div>
    @endunless

    {{-- ── Unit Pelayanan ─────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <div class="text-sm font-bold text-navy">Unit Pelayanan &amp; Penanggung Jawab</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $jumlahUnit }} unit terdaftar</div>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('admin.config') }}" class="flex items-center gap-2 border border-slate-200 rounded-lg px-3 py-1.5">
                    <span class="material-icons-outlined text-slate-400" style="font-size: 15px;">search</span>
                    <input type="text" name="cari_unit" value="{{ $cari }}" placeholder="Cari unit…"
                           class="w-36 text-xs focus:outline-none text-slate-700 placeholder:text-slate-400">
                    @if ($cari !== '')
                        <a href="{{ route('admin.config') }}" class="text-slate-400 hover:text-slate-600" title="Hapus pencarian">
                            <span class="material-icons-outlined" style="font-size: 14px;">close</span>
                        </a>
                    @endif
                </form>
                @if ($bolehUbah)
                    <button type="button" @click="unitBaru()"
                            class="text-xs text-emerald-700 font-semibold border border-emerald-200 rounded-lg px-3 py-1.5 hover:bg-emerald-50 transition flex items-center gap-1">
                        <span class="material-icons-outlined" style="font-size: 14px;">add</span>
                        Tambah Unit
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        @foreach (['Unit', 'Penanggung Jawab', 'Kontak WA', ''] as $h)
                            <th class="text-left py-2 px-3 text-xs font-bold text-slate-400">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unit as $u)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-3">
                                <div class="font-semibold text-navy">{{ $u->nama }}
                                    @unless ($u->aktif)
                                        <span class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500">Nonaktif</span>
                                    @endunless
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $u->kode }} · {{ $u->kelompok->label() }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-600">{{ $u->penanggung_jawab ?: '—' }}</td>
                            <td class="py-3 px-3 text-slate-600 font-mono text-xs">{{ $u->no_wa ? '+' . $u->no_wa : '—' }}</td>
                            <td class="py-3 px-3">
                                @if ($bolehUbah)
                                    <button type="button" class="text-slate-400 hover:text-navy transition" title="Ubah"
                                            @click="ubahUnit(@js(['id' => $u->id, 'kode' => $u->kode, 'nama' => $u->nama, 'kelompok' => $u->kelompok->value, 'penanggung_jawab' => $u->penanggung_jawab ?? '', 'no_wa' => $u->no_wa ?? '', 'aktif' => (bool) $u->aktif]))">
                                        <span class="material-icons-outlined" style="font-size: 15px;">edit</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-slate-400 text-sm">Unit tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-admin.pagination :paginator="$unit" />
    </div>

    {{-- ── Hak Akses Staf ─────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-1">
            <div class="text-sm font-bold text-navy">Hak Akses Staf</div>
            @if ($bolehUbah)
                <button type="button" @click="stafBaru()"
                        class="text-xs text-emerald-700 font-semibold border border-emerald-200 rounded-lg px-3 py-1.5 hover:bg-emerald-50 transition flex items-center gap-1">
                    <span class="material-icons-outlined" style="font-size: 14px;">person_add</span>
                    Tambah Staf
                </button>
            @endif
        </div>

        <div class="flex flex-wrap gap-2 mb-4 mt-2">
            @foreach ($peran as $p)
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $lencanaPeran[$p->value] }}">{{ $p->label() }}</span>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        @foreach (['Nama', 'Peran', 'Unit / Poli', 'Hak Akses', ''] as $h)
                            <th class="text-left py-2.5 px-3 text-xs font-bold text-slate-400">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staf as $s)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3 px-3">
                                <div class="font-semibold text-navy">{{ $s->name }}
                                    @unless ($s->aktif)
                                        <span class="ml-1 text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500">Nonaktif</span>
                                    @endunless
                                </div>
                                <div class="text-[11px] text-slate-400">{{ $s->email }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $lencanaPeran[$s->peran->value] }}">{{ $s->peran->label() }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if ($s->unit)
                                    <span class="text-xs text-slate-700 font-medium">{{ $s->unit->nama }}</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Semua Unit</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-slate-500 text-xs">{{ $s->peran->deskripsi() }}</td>
                            <td class="py-3 px-3">
                                @if ($bolehUbah)
                                    <button type="button" class="text-slate-400 hover:text-navy transition" title="Ubah"
                                            @click="ubahStaf(@js(['id' => $s->id, 'name' => $s->name, 'email' => $s->email, 'password' => '', 'peran' => $s->peran->value, 'unit_id' => $s->unit_id ?? '', 'no_wa' => $s->no_wa ?? '', 'aktif' => (bool) $s->aktif]))">
                                        <span class="material-icons-outlined" style="font-size: 15px;">edit</span>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($peran as $p)
                <div class="flex items-start gap-2.5 text-xs text-slate-500">
                    <span class="mt-0.5 text-xs font-semibold px-2 py-0.5 rounded-full shrink-0 {{ $lencanaPeran[$p->value] }}">{{ $p->label() }}</span>
                    <span>{{ $p->deskripsi() }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Pengaturan Otomatis ────────────────────────── --}}
    <form method="POST" action="{{ route('admin.config.settings') }}" x-ref="formPengaturan"
          x-data="{ s: @js($pengaturan) }"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        @csrf
        <div class="text-sm font-bold mb-4 text-navy">Pengaturan Otomatis</div>
        <div class="space-y-3">
            @foreach ($pengaturanLabel as $kunci => $label)
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <span class="text-sm text-slate-600">{{ $label }}</span>
                    <input type="hidden" name="{{ $kunci }}" :value="s.{{ $kunci }} ? 1 : 0">
                    <button type="button" @if ($bolehUbah) @click="s.{{ $kunci }} = !s.{{ $kunci }}; $nextTick(() => $refs.formPengaturan.submit())" @else disabled @endif
                            :class="s.{{ $kunci }} ? 'bg-emerald-500' : 'bg-slate-200'"
                            class="w-11 h-6 rounded-full transition-colors relative shrink-0 ml-4 {{ $bolehUbah ? '' : 'opacity-60 cursor-not-allowed' }}"
                            aria-label="{{ $label }}">
                        <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                              :class="s.{{ $kunci }} ? 'translate-x-5' : 'translate-x-0.5'"></span>
                    </button>
                </div>
            @endforeach
        </div>
        <div class="mt-3 text-xs text-slate-400 leading-relaxed">
            Pilihan ini disimpan di database. Menjalankan otomatisasinya (kirim WhatsApp otomatis dan tutup tiket terjadwal)
            membutuhkan layanan tambahan dan belum aktif.
        </div>
    </form>

    {{-- ── Jendela: Unit ─────────────────────────────── --}}
    @if ($bolehUbah)
        <div x-show="modal === 'unit'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background-color: rgba(15,46,90,0.25); backdrop-filter: blur(2px);" @keydown.escape.window="modal = null">
            <form method="POST" :action="unit.id ? '{{ url('/admin/konfigurasi/unit') }}/' + unit.id : '{{ url('/admin/konfigurasi/unit') }}'"
                  class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-4 max-h-[92vh] overflow-y-auto"
                  style="animation: fadeIn .2s ease;">
                @csrf
                <input type="hidden" name="_method" value="PUT" :disabled="!unit.id">
                <input type="hidden" name="_modal" value="unit">
                <input type="hidden" name="_id" :value="unit.id || ''">

                <div class="flex items-center justify-between">
                    <div class="text-base font-bold text-navy" x-text="unit.id ? 'Ubah Unit' : 'Tambah Unit'"></div>
                    <button type="button" @click="modal = null" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup">
                        <span class="material-icons-outlined" style="font-size: 20px;">close</span>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Kode</label>
                        <input type="text" name="kode" x-model="unit.kode" maxlength="10" placeholder="B0099"
                               class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:border-navy transition">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Nama Unit / Poli</label>
                        <input type="text" name="nama" x-model="unit.nama" maxlength="100" placeholder="cth. POLI ANAK"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-navy">Kelompok</label>
                    <select name="kelompok" x-model="unit.kelompok"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition bg-white">
                        @foreach ($kelompok as $k)
                            <option value="{{ $k->value }}">{{ $k->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Penanggung Jawab</label>
                        <input type="text" name="penanggung_jawab" x-model="unit.penanggung_jawab" maxlength="100"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Nomor WhatsApp</label>
                        <input type="text" name="no_wa" x-model="unit.no_wa" maxlength="20" placeholder="0812-3456-7890"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-navy transition">
                    </div>
                </div>
                <div class="text-xs text-slate-400 -mt-2">Nomor ditulis bebas (0812…, +62 812…), otomatis diubah ke format 62812….</div>

                <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                    <input type="hidden" name="aktif" value="0">
                    <input type="checkbox" name="aktif" value="1" x-model="unit.aktif" class="w-4 h-4 accent-emerald-600">
                    Unit aktif (tampil sebagai tujuan penerusan tiket)
                </label>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modal = null"
                            class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:border-slate-300 transition">Batal</button>
                    <button type="submit" class="flex-1 grad-btn text-white text-sm font-semibold py-2.5 rounded-xl transition">Simpan Unit</button>
                </div>
            </form>
        </div>

        {{-- ── Jendela: Staf ─────────────────────────── --}}
        <div x-show="modal === 'staf'" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background-color: rgba(15,46,90,0.25); backdrop-filter: blur(2px);" @keydown.escape.window="modal = null">
            <form method="POST" :action="staf.id ? '{{ url('/admin/konfigurasi/staf') }}/' + staf.id : '{{ url('/admin/konfigurasi/staf') }}'"
                  class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-4 max-h-[92vh] overflow-y-auto"
                  style="animation: fadeIn .2s ease;">
                @csrf
                <input type="hidden" name="_method" value="PUT" :disabled="!staf.id">
                <input type="hidden" name="_modal" value="staf">
                <input type="hidden" name="_id" :value="staf.id || ''">

                <div class="flex items-center justify-between">
                    <div class="text-base font-bold text-navy" x-text="staf.id ? 'Ubah Staf' : 'Tambah Staf'"></div>
                    <button type="button" @click="modal = null" class="text-slate-400 hover:text-slate-600 transition" aria-label="Tutup">
                        <span class="material-icons-outlined" style="font-size: 20px;">close</span>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-navy">Nama Lengkap / Jabatan</label>
                    <input type="text" name="name" x-model="staf.name" maxlength="100" placeholder="cth. dr. Rizal Fahmi"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy">Email (untuk login)</label>
                        <input type="email" name="email" x-model="staf.email" maxlength="255" placeholder="nama@rsud-damanhuri.id"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-navy"
                               x-text="staf.id ? 'Kata sandi baru' : 'Kata sandi'"></label>
                        <input type="password" name="password" x-model="staf.password" maxlength="100" autocomplete="new-password"
                               :placeholder="staf.id ? 'Kosongkan bila tetap' : 'Minimal 8 karakter'"
                               class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-navy">Peran</label>
                    <select name="peran" x-model="staf.peran" :disabled="sendiri"
                            class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-navy transition bg-white disabled:bg-slate-50 disabled:text-slate-400">
                        @foreach ($peran as $p)
                            <option value="{{ $p->value }}">{{ $p->label() }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="peran" :value="staf.peran" x-show="false" :disabled="!sendiri">
                    <div class="mt-1.5 text-xs text-slate-400" x-text="deskripsi[staf.peran]"></div>
                </div>

                <div x-show="staf.peran === 'petugas_unit'" x-cloak>
                    <label class="block text-xs font-semibold mb-1.5 text-navy">Unit / Poli <span class="text-red-400">*</span></label>
                    <x-admin.unit-picker :units="$daftarUnit" name="unit_id" x-model="staf.unit_id" placeholder="Cari unit/poli…" />
                    <div class="mt-1.5 text-xs text-slate-400">Staf hanya dapat melihat tiket yang masuk ke unit ini.</div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-navy">Nomor WhatsApp (opsional)</label>
                    <input type="text" name="no_wa" x-model="staf.no_wa" maxlength="20" placeholder="0812-3456-7890"
                           class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-navy transition">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600" :class="sendiri ? 'opacity-50' : 'cursor-pointer'">
                    <input type="hidden" name="aktif" value="0">
                    <input type="checkbox" name="aktif" value="1" x-model="staf.aktif" :disabled="sendiri" class="w-4 h-4 accent-emerald-600">
                    Akun aktif (boleh login)
                </label>
                <div x-show="sendiri" x-cloak class="text-xs text-amber-600 -mt-2">
                    Ini akun Anda sendiri: peran dan status aktif tidak dapat diubah agar Anda tidak terkunci.
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modal = null"
                            class="flex-1 border border-slate-200 text-slate-600 text-sm font-semibold py-2.5 rounded-xl hover:border-slate-300 transition">Batal</button>
                    <button type="submit" class="flex-1 grad-btn text-white text-sm font-semibold py-2.5 rounded-xl transition">Simpan Staf</button>
                </div>
            </form>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', function () {
        Alpine.data('konfigurasi', function (awal) {
            return {
                modal: awal.modal, unit: awal.unit, staf: awal.staf,
                diriId: awal.diriId, deskripsi: awal.deskripsi,
                get sendiri() { return !!this.staf.id && this.staf.id === this.diriId; },
                unitBaru() { this.unit = { id: null, kode: '', nama: '', kelompok: 'poli', penanggung_jawab: '', no_wa: '', aktif: true }; this.modal = 'unit'; },
                ubahUnit(u) { this.unit = Object.assign({}, u); this.modal = 'unit'; },
                stafBaru() { this.staf = { id: null, name: '', email: '', password: '', peran: 'petugas_unit', unit_id: '', no_wa: '', aktif: true }; this.modal = 'staf'; },
                ubahStaf(s) { this.staf = Object.assign({}, s); this.modal = 'staf'; }
            };
        });
    });
</script>
@endpush
