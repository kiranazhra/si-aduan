@extends('layouts.app')

@section('title', 'Ajukan Pengaduan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-14"
     x-data="{
        step: 1,
        name: '',
        phone: '',
        address: '',
        category: '',
        location: '',
        date: '',
        time: '',
        title: '',
        desc: '',
        rating: {{ (int) old('rating', 0) }},
        hoverRating: 0,
        ratingLabels: { 1: 'Tidak Puas', 2: 'Kurang Puas', 3: 'Cukup Puas', 4: 'Puas', 5: 'Sangat Puas' },
        files: [],
        galatFile: '',
        maxFile: 5,
        tambahFile(e) {
            const input = e.target;
            const dt = input._dt || (input._dt = new DataTransfer());
            this.galatFile = '';
            for (const f of Array.from(input.files)) {
                if (!/\.(jpe?g|png|pdf)$/i.test(f.name)) { this.galatFile = f.name + ' bukan file JPG, PNG, atau PDF.'; continue; }
                if (f.size > 5 * 1024 * 1024) { this.galatFile = f.name + ' lebih dari 5 MB dan tidak ditambahkan.'; continue; }
                if (dt.files.length >= this.maxFile) { this.galatFile = 'Maksimal ' + this.maxFile + ' file.'; break; }
                if (Array.from(dt.files).some(x => x.name === f.name && x.size === f.size && x.lastModified === f.lastModified)) continue;
                dt.items.add(f);
            }
            input.files = dt.files;
            this.files = Array.from(dt.files).map(f => ({ name: f.name, size: f.size }));
        },
        hapusFile(i) {
            const input = this.$refs.berkas;
            const dt = new DataTransfer();
            Array.from(input._dt.files).forEach((f, n) => { if (n !== i) dt.items.add(f); });
            input._dt = dt;
            input.files = dt.files;
            this.galatFile = '';
            this.files = Array.from(dt.files).map(f => ({ name: f.name, size: f.size }));
        },
        get canNext1() { return this.name && this.phone && this.address },
        get canNext2() { return this.category && this.location && this.date && this.time && this.title && this.desc },
     }">

    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-0 mb-10">
        <template x-for="(label, idx) in ['Data Pelapor', 'Detail Pengaduan', 'Konfirmasi']" :key="idx">
            <div class="flex items-center">
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold border-2 shadow-sm transition-all"
                         :class="step > idx + 1 ? 'grad-btn border-transparent text-white' : (step === idx + 1 ? 'border-navy bg-white text-navy' : 'border-white bg-white/80 text-slate-500')">
                        <span x-show="step > idx + 1" class="material-icons-outlined text-white text-base">check</span>
                        <span x-show="step <= idx + 1" x-text="idx + 1"></span>
                    </div>
                    <div class="text-xs mt-1 font-semibold [text-shadow:0_1px_6px_rgba(0,0,0,0.5)]"
                         :class="step === idx + 1 ? 'text-white' : (step > idx + 1 ? 'text-emerald-300' : 'text-slate-200')"
                         x-text="label"></div>
                </div>
                <div class="w-16 h-px mx-2 mb-5" :class="step > idx + 1 ? 'bg-emerald-400' : 'bg-slate-200'" x-show="idx < 2"></div>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">

        {{-- Pesan error validasi (kalau ada, dari server) --}}
        @if ($errors->any())
            <div class="mb-5 bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl px-4 py-3">
                <div class="font-semibold mb-1">Periksa kembali data Anda:</div>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('complaint.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Step 1: Data Pelapor --}}
            <div x-show="step === 1" x-cloak class="space-y-5">
                <div>
                    <h2 class="text-xl font-bold text-navy">Data Pelapor</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Pilih identitas yang ingin Anda gunakan. Kolom bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.</p>
                </div>

                <input type="hidden" name="anonim" value="0">

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pelapor" x-model="name" placeholder="Nama sesuai KTP" required maxlength="100"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="no_wa_pelapor" x-model="phone" placeholder="08xxxxxxxxxx" required maxlength="20" inputmode="tel"
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition">
                        <p class="mt-1.5 text-xs text-slate-400 flex items-start gap-1">
                            <span class="material-icons-outlined shrink-0" style="font-size: 16px;">info</span>
                            Isi dengan nomor WhatsApp yang aktif. Tindak lanjut dan jawaban pengaduan akan dikirim ke nomor ini.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-1.5">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="alamat_pelapor" x-model="address" rows="2" required maxlength="255" placeholder="Alamat tempat tinggal Anda"
                                  class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition resize-none" ></textarea>
                    </div>
                </div>

                <button type="button" @click="step = 2" :disabled="!canNext1"
                        class="w-full grad-btn text-white font-semibold py-3.5 rounded-xl disabled:opacity-40 transition-all">
                    Lanjut
                </button>
            </div>

            {{-- Step 2: Detail Pengaduan --}}
            <div x-show="step === 2" x-cloak class="space-y-5">
                <div>
                    <h2 class="text-xl font-bold text-navy">Detail Pengaduan</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Jelaskan pengaduan Anda secara singkat dan jelas. Kolom bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori_id" x-model="category" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition bg-white">
                        <option value="">Pilih kategori</option>
                        @foreach ($kategoris as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy mb-1.5">Unit / Poli Kejadian <span class="text-red-500">*</span></label>
                    <select name="lokasi_id" x-model="location" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition bg-white">
                        <option value="">Pilih unit/poli</option>
                        @foreach ($unitPerKelompok as $grup)
                            <optgroup label="{{ $grup['label'] }}">
                                @foreach ($grup['daftar'] as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-1.5">Tanggal Kejadian <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_kejadian" x-model="date" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-navy mb-1.5">Waktu Kejadian <span class="text-red-500">*</span></label>
                        <input type="time" name="waktu_kejadian" x-model="time" required
                               class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy mb-1.5">Judul Singkat <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" x-model="title" placeholder="Ringkasan masalah dalam 1 kalimat" required maxlength="150"
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-navy mb-1.5">Uraian Pengaduan <span class="text-red-500">*</span></label>
                    <textarea name="uraian" x-model="desc" rows="4" required placeholder="Jelaskan kejadian secara rinci..."
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-navy transition resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="step = 1"
                            class="flex-1 border border-slate-200 text-slate-600 font-semibold py-3.5 rounded-xl hover:border-slate-300 transition">Kembali</button>
                    <button type="button" @click="step = 3" :disabled="!canNext2"
                            class="flex-1 grad-btn text-white font-semibold py-3.5 rounded-xl disabled:opacity-40 transition">Lanjut</button>
                </div>
            </div>

            {{-- Step 3: Upload & Konfirmasi --}}
            <div x-show="step === 3" x-cloak class="space-y-5">
                <div>
                    <h2 class="text-xl font-bold text-navy">Unggah Bukti & Konfirmasi</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Periksa kembali sebelum mengirim. Kolom bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.</p>
                </div>

                <div>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center">
                        <span class="material-icons-outlined text-slate-300 block mb-2 text-4xl">cloud_upload</span>
                        <div class="text-sm text-slate-500">Foto atau dokumen bukti <span class="text-red-500 font-semibold">*</span></div>
                        <div class="text-xs text-slate-400 mt-0.5">Wajib minimal 1 file. JPG/PNG/PDF, maks. 5 file, masing-masing maks. 5 MB.</div>
                        <label class="mt-3 inline-block text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-lg px-4 py-2 cursor-pointer hover:bg-emerald-50 transition"
                               x-show="files.length < maxFile">
                            <span x-text="files.length ? 'Tambah File' : 'Pilih File'">Pilih File</span>
                            <input type="file" name="lampiran[]" x-ref="berkas" class="hidden" accept="image/*,.pdf" multiple required
                                   @change="tambahFile($event)">
                        </label>
                        <div x-show="galatFile" x-text="galatFile" class="mt-2 text-xs text-red-500"></div>
                    </div>

                    <ul x-show="files.length" class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.name + f.size + i">
                            <li class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-2.5 text-sm">
                                <span class="material-icons-outlined text-slate-400 text-lg">attach_file</span>
                                <span class="flex-1 min-w-0 truncate text-navy font-medium" x-text="f.name"></span>
                                <span class="text-xs text-slate-400 shrink-0" x-text="(f.size / 1048576).toFixed(2) + ' MB'"></span>
                                <button type="button" @click="hapusFile(i)" class="text-slate-400 hover:text-red-500 transition" aria-label="Hapus file">
                                    <span class="material-icons-outlined text-lg">close</span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>


                {{-- Penilaian pelayanan --}}
                <div>
                    <label class="block text-sm font-semibold text-navy mb-0.5">Penilaian Pelayanan <span class="text-red-500">*</span></label>
                    <p class="text-xs text-slate-400 mb-2">Seberapa puas Anda dengan pelayanan RSUD H. Damanhuri Barabai?</p>

                    <input type="hidden" name="rating" :value="rating">

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1" @mouseleave="hoverRating = 0">
                        <div class="flex items-center">
                            <template x-for="n in [1, 2, 3, 4, 5]" :key="n">
                                <button type="button" @click="rating = n" @mouseenter="hoverRating = n"
                                        :aria-label="n + ' bintang - ' + ratingLabels[n]"
                                        class="p-0.5 transition-transform hover:scale-110 focus:outline-none">
                                    <svg viewBox="0 0 24 24" class="w-9 h-9 transition-colors" fill="currentColor"
                                         :class="(hoverRating || rating) >= n ? 'text-amber-400' : 'text-slate-200'">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <div class="text-sm font-semibold"
                             :class="(hoverRating || rating) ? 'text-navy' : 'text-slate-400'"
                             x-text="(hoverRating || rating) ? ratingLabels[hoverRating || rating] + ' (' + (hoverRating || rating) + ')' : 'Pilih jumlah bintang'"></div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="font-bold text-xs text-slate-400 uppercase tracking-wide mb-3">Ringkasan</div>
                    <div class="flex gap-2">
                        <span class="text-slate-400 w-20 shrink-0">Pelapor</span>
                        <span class="font-medium text-navy" x-text="name || '-'"></span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-slate-400 w-20 shrink-0">Judul</span>
                        <span class="font-medium text-navy" x-text="title"></span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-slate-400 w-20 shrink-0">Tanggal</span>
                        <span class="font-medium text-navy" x-text="date + (time ? ' ' + time : '')"></span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-slate-400 w-20 shrink-0">Lampiran</span>
                        <span class="font-medium text-navy" x-text="files.length + ' file'"></span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-slate-400 w-20 shrink-0">Penilaian</span>
                        <span class="font-medium text-navy" x-text="rating ? rating + ' bintang - ' + ratingLabels[rating] : '-'"></span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="step = 2"
                            class="flex-1 border border-slate-200 text-slate-600 font-semibold py-3.5 rounded-xl hover:border-slate-300 transition">Kembali</button>
                    <button type="submit" :disabled="!files.length || !rating"
                            class="flex-1 grad-btn text-white font-semibold py-3.5 rounded-xl shadow-md disabled:opacity-40 transition">
                        Kirim Pengaduan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
