<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisRiwayat;
use App\Enums\PeranPengguna;
use App\Enums\PrioritasAduan;
use App\Enums\StatusAduan;
use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Models\PesanWhatsapp;
use App\Models\Unit;
use App\Support\PesanValidasi;
use App\Support\Wa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TiketController extends Controller
{
    /** Halaman "Semua Tiket Masuk". */
    public function index(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $query = Aduan::with(['kategori', 'unit'])
            ->latest('dibuat_pada')
            ->latest('id');

        if (StatusAduan::tryFrom($status)) {
            $query->where('status', $status);
        } else {
            $status = '';
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nomor_tiket', 'like', "%{$q}%")
                  ->orWhere('nama_pelapor', 'like', "%{$q}%")
                  ->orWhere('judul', 'like', "%{$q}%");
            });
        }

        $tiket      = $query->paginate(15)->withQueryString();
        $totalSemua = Aduan::count();

        return view('admin.tiket.index', compact('tiket', 'totalSemua', 'q', 'status'));
    }

    /** Halaman detail tiket. */
    public function show(Request $request, Aduan $aduan)
    {
        $aduan->load(['kategori', 'lokasi', 'unit', 'lampiran', 'riwayat.petugas', 'riwayat.unit']);

        $units = Unit::aktif()->orderBy('nama')->get(['id', 'nama', 'no_wa'])
            ->map(fn ($u) => ['id' => $u->id, 'nama' => $u->nama, 'wa' => Wa::normalisasi($u->no_wa)])
            ->values();

        // Langkah alur: 1 = belum diteruskan, 2 = di unit, 3 = selesai (siap kirim solusi), 4 = solusi terkirim
        $langkah = $aduan->solusi_dikirim_pada ? 4
            : ($aduan->status === StatusAduan::Selesai ? 3
            : ($aduan->unit_id ? 2 : 1));

        return view('admin.tiket.show', [
            'aduan'          => $aduan,
            'units'          => $units,
            'langkah'        => $langkah,
            'bolehUbah'      => $request->user()->bolehKoordinasi(),
            'pesanDisposisi' => Wa::pesanDisposisi($aduan),
            'opsiGrading'    => $this->opsiGrading($aduan),
            'templatSolusi'  => Wa::pesanSolusi($aduan, '__SOLUSI__'),
            'waPelapor'      => $aduan->anonim ? null : Wa::normalisasi($aduan->no_wa_pelapor),
            'jawabanUnit'    => $this->jawabanUnit($aduan),
            'pesanSolusiTerkirim' => $aduan->pesanWhatsapp()->where('jenis_penerima', 'pelapor')->latest('id')->first(),
        ]);
    }

    /**
     * Pilihan grading untuk jendela "Teruskan ke Unit", lengkap dengan batas waktu masing-masing
     * (dihitung sejak aduan diterima) supaya pratinjau pesan WhatsApp ikut berubah saat dipilih.
     *
     * @return array<int, array{nilai: string, label: string, keterangan: string, waktu: string, batas: string}>
     */
    private function opsiGrading(Aduan $aduan): array
    {
        return collect(PrioritasAduan::pilihan())->map(fn (PrioritasAduan $g) => [
            'nilai'      => $g->value,
            'label'      => $g->label(),
            'keterangan' => $g->keterangan(),
            'waktu'      => $g->waktuLabel(),
            'batas'      => $g->batasWaktu($aduan->dibuat_pada)->locale('id')->translatedFormat('d F Y · H.i'),
        ])->all();
    }

    /**
     * Jawaban terakhir dari petugas unit saat menandai tiket Selesai. Dipakai untuk mengisi
     * form solusi admin secara otomatis. Catatan admin sendiri dan catatan sementara
     * (mis. saat status masih Diproses) tidak ikut diambil.
     *
     * @return array{teks: string, petugas: string, unit: ?string}|null
     */
    private function jawabanUnit(Aduan $aduan): ?array
    {
        $riwayat = $aduan->riwayat
            ->filter(fn ($r) => $r->jenis === JenisRiwayat::Tanggapan
                && $r->status === StatusAduan::Selesai
                && filled($r->catatan)
                && $r->petugas?->peran === PeranPengguna::PetugasUnit)
            ->last();

        return $riwayat ? [
            'teks'    => trim($riwayat->catatan),
            'petugas' => $riwayat->petugas->name,
            'unit'    => $aduan->unit?->nama,
        ] : null;
    }

    /** Teruskan tiket ke unit + tentukan grading (status otomatis menjadi Dikoordinasikan). */
    public function teruskan(Request $request, Aduan $aduan)
    {
        $pengguna = $request->user();
        abort_unless($pengguna->bolehKoordinasi(), 403);

        if ($aduan->status === StatusAduan::Selesai) {
            return back()->with('error', 'Tiket yang sudah selesai tidak dapat diteruskan.');
        }

        $data = $request->validate([
            'unit_id'   => ['required', Rule::exists('unit', 'id')->where('aktif', 1)],
            'prioritas' => ['required', Rule::in(array_column(PrioritasAduan::cases(), 'value'))],
        ], PesanValidasi::UMUM, ['unit_id' => 'Unit tujuan', 'prioritas' => 'Grading']);

        $unit    = Unit::findOrFail($data['unit_id']);
        $grading = PrioritasAduan::from($data['prioritas']);
        $aduan->loadMissing(['kategori', 'lokasi']);

        DB::transaction(function () use ($aduan, $unit, $pengguna, $grading) {
            $aduan->teruskanKe($unit, $pengguna, $grading);

            PesanWhatsapp::create([
                'aduan_id'       => $aduan->id,
                'dikirim_oleh'   => $pengguna->id,
                'jenis_penerima' => 'unit',
                'no_wa_penerima' => Wa::normalisasi($unit->no_wa) ?? '',
                'isi_pesan'      => Wa::pesanDisposisi($aduan, $grading),
                'status'         => 'dibuka',
            ]);
        });

        return redirect()->route('admin.tickets.show', ['aduan' => $aduan->nomor_tiket])
            ->with('success', "Tiket diteruskan ke {$unit->nama} dengan grading {$grading->label()} ({$grading->waktuLabel()}).");
    }

    /** Perbarui status (Diproses / Dikoordinasikan / Selesai) dengan catatan opsional. */
    public function ubahStatus(Request $request, Aduan $aduan)
    {
        $pengguna = $request->user();
        abort_unless($pengguna->bolehKoordinasi(), 403);

        if ($aduan->solusi_dikirim_pada) {
            return back()->with('error', 'Solusi sudah dikirim, status tidak dapat diubah lagi.');
        }

        $data = $request->validate([
            'status'  => ['required', Rule::in(array_column(StatusAduan::cases(), 'value'))],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], PesanValidasi::UMUM, ['status' => 'Status', 'catatan' => 'Catatan']);

        $aduan->ubahStatus(StatusAduan::from($data['status']), $pengguna, $data['catatan'] ?? null);

        return redirect()->route('admin.tickets.show', ['aduan' => $aduan->nomor_tiket])->with('success', 'Status tiket diperbarui.');
    }

    /** Simpan solusi dan (bila pelapor punya WhatsApp) catat pengiriman lewat WhatsApp. */
    public function kirimSolusi(Request $request, Aduan $aduan)
    {
        $pengguna = $request->user();
        abort_unless($pengguna->bolehKoordinasi(), 403);

        if ($aduan->status !== StatusAduan::Selesai) {
            return back()->with('error', 'Solusi baru bisa dikirim setelah status tiket Selesai.');
        }
        if ($aduan->solusi_dikirim_pada) {
            return back()->with('error', 'Solusi untuk tiket ini sudah dikirim.');
        }

        $data = $request->validate([
            'solusi' => ['required', 'string', 'min:5', 'max:2000'],
        ], PesanValidasi::UMUM, ['solusi' => 'Solusi']);

        $noWa = $aduan->anonim ? null : Wa::normalisasi($aduan->no_wa_pelapor);

        DB::transaction(function () use ($aduan, $data, $pengguna, $noWa) {
            $aduan->update([
                'solusi'              => $data['solusi'],
                'diselesaikan_oleh'   => $pengguna->id,
                'solusi_dikirim_pada' => now(),
            ]);

            if ($noWa) {
                PesanWhatsapp::create([
                    'aduan_id'       => $aduan->id,
                    'dikirim_oleh'   => $pengguna->id,
                    'jenis_penerima' => 'pelapor',
                    'no_wa_penerima' => $noWa,
                    'isi_pesan'      => Wa::pesanSolusi($aduan, $data['solusi']),
                    'status'         => 'dibuka',
                ]);
                $aduan->catat(JenisRiwayat::SolusiDikirim, 'Solusi dikirim ke WhatsApp pelapor', $pengguna);
            } else {
                $aduan->catat(JenisRiwayat::SolusiDikirim, 'Solusi disimpan (pelapor tanpa WhatsApp, solusi tampil di Cek Status)', $pengguna);
            }
        });

        return redirect()->route('admin.tickets.show', ['aduan' => $aduan->nomor_tiket])
            ->with('success', $noWa
                ? 'Solusi tersimpan dan dikirim ke WhatsApp pelapor.'
                : 'Solusi tersimpan. Pelapor dapat melihatnya di halaman Cek Status.');
    }
}
