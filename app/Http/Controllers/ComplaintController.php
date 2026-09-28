<?php

namespace App\Http\Controllers;

use App\Enums\KelompokUnit;
use App\Models\Aduan;
use App\Models\Kategori;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    public function create()
    {
        $kategoris = Kategori::where('aktif', 1)->orderBy('urutan')->get();

        // Pilihan "Lokasi Kejadian" di form sekarang daftar 98 Unit/Poli (tabel unit),
        // dikelompokkan (optgroup) supaya tidak jadi satu daftar panjang 98 baris.
        $unitPerKelompok = $this->unitPerKelompokUntukForm();

        return view('complaint-form', compact('kategoris', 'unitPerKelompok'));
    }

    /**
     * Unit aktif, dikelompokkan sesuai urutan enum KelompokUnit, untuk dropdown form.
     * Kelompok "Lainnya / Eksternal" sengaja tidak ditampilkan di form pelapor —
     * isinya bukan lokasi fisik tempat pasien bisa mengalami kejadian (mis. GFK,
     * PBF, Pihak Ke-3/TIM JKN, Portal, Rumah Sakit Balangan, Expired Date).
     */
    private function unitPerKelompokUntukForm()
    {
        $unit = Unit::aktif()->orderBy('nama')->get()->groupBy(fn (Unit $u) => $u->kelompok->value);

        return collect(KelompokUnit::cases())
            ->reject(fn (KelompokUnit $k) => $k === KelompokUnit::Lainnya)
            ->map(fn (KelompokUnit $k) => [
                'label'  => $k->label(),
                'daftar' => $unit->get($k->value, collect()),
            ])
            ->filter(fn (array $grup) => $grup['daftar']->isNotEmpty())
            ->values();
    }

    public function store(Request $request)
    {
        $anonim = (bool) $request->input('anonim', 0);

        $validated = $request->validate([
            'nama_pelapor'     => $anonim ? 'nullable' : 'required|string|max:100',
            'no_wa_pelapor'    => $anonim ? 'nullable' : 'required|string|max:20',
            'alamat_pelapor'   => $anonim ? 'nullable' : 'required|string|max:255',
            'kategori_id'      => 'required|exists:kategori,id',
            'lokasi_id'        => 'required|exists:unit,id',
            'tanggal_kejadian' => 'required|date',
            'waktu_kejadian'   => 'required|date_format:H:i',
            'judul'            => 'required|string|max:150',
            'uraian'           => 'required|string',
            'rating'           => 'required|integer|between:1,5',
            'lampiran'         => 'required|array|min:1|max:5',
            'lampiran.*'       => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'alamat_pelapor.required' => 'Alamat wajib diisi.',
            'waktu_kejadian.required' => 'Waktu kejadian wajib diisi.',
            'rating.required'     => 'Silakan beri penilaian pelayanan (1 sampai 5 bintang).',
            'rating.between'      => 'Penilaian pelayanan harus 1 sampai 5 bintang.',
            'lampiran.required'   => 'Foto atau dokumen bukti wajib diunggah (minimal 1 file).',
            'lampiran.min'        => 'Foto atau dokumen bukti wajib diunggah (minimal 1 file).',
            'lampiran.max'        => 'Maksimal 5 file bukti.',
            'lampiran.*.mimes'    => 'Setiap bukti harus berupa file JPG, PNG, atau PDF.',
            'lampiran.*.max'      => 'Ukuran tiap file bukti maksimal 5 MB.',
            'lampiran.*.uploaded' => 'Ada file bukti yang gagal diunggah. Pastikan tiap file tidak lebih dari 5 MB.',
        ]);

        // Nomor tiket: {tanggal}-{kode kategori}-{kode lokasi/poli}-{kode acak}
        // Contoh: 210926-RI-IGD-K7M2
        $nomorTiket = Aduan::buatNomorTiket((int) $validated['kategori_id'], (int) $validated['lokasi_id']);

        // Aduan dan semua lampirannya disimpan bersama: bila ada yang gagal, tidak ada tiket setengah jadi.
        $tersimpan = [];

        try {
            $aduan = DB::transaction(function () use ($request, $validated, $anonim, $nomorTiket, &$tersimpan) {
                $aduan = Aduan::create([
                    'nomor_tiket'      => $nomorTiket,
                    'anonim'           => $anonim ? 1 : 0,
                    'nama_pelapor'     => $anonim ? null : $validated['nama_pelapor'],
                    'no_wa_pelapor'    => $anonim ? null : $validated['no_wa_pelapor'],
                    'alamat_pelapor'   => $anonim ? null : ($validated['alamat_pelapor'] ?? null),
                    'kategori_id'      => $validated['kategori_id'],
                    'lokasi_id'        => $validated['lokasi_id'],
                    'tanggal_kejadian' => $validated['tanggal_kejadian'] . ' ' . $validated['waktu_kejadian'],
                    'judul'            => $validated['judul'],
                    'uraian'           => $validated['uraian'],
                    'rating'           => (int) $validated['rating'],
                    'media'            => 'portal',
                    'prioritas'        => 'sedang',
                    'status'           => 'diproses',
                ]);

                foreach ($request->file('lampiran') as $berkas) {
                    $mime   = $berkas->getMimeType() ?: 'application/octet-stream';
                    $ukuran = $berkas->getSize();
                    $tersimpan[] = $path = $berkas->store('lampiran-aduan', 'public');

                    $aduan->lampiran()->create([
                        'alamat_file' => $path,
                        'nama_file'   => Str::limit($berkas->getClientOriginalName(), 200, ''),
                        'jenis_file'  => $mime,
                        'ukuran'      => $ukuran,
                    ]);
                }

                return $aduan;
            });
        } catch (\Throwable $e) {
            // Transaksi database sudah dibatalkan; hapus juga file yang sempat tersimpan.
            Storage::disk('public')->delete($tersimpan);

            throw $e;
        }

        return redirect()
            ->route('complaint.confirmation', ['ticket' => $aduan->nomor_tiket]);
    }

    public function confirmation(Request $request)
    {
        $ticket = $request->query('ticket');

        abort_if(!$ticket, 404);

        return view('ticket-confirmation', ['ticket' => $ticket]);
    }
}
