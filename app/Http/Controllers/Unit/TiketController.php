<?php

namespace App\Http\Controllers\Unit;

use App\Enums\StatusAduan;
use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Support\EksporTiketUnit;
use App\Support\PeriodeFilter;
use App\Support\PesanValidasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TiketController extends Controller
{
    /** Halaman "Tiket Unit Saya" — hanya aduan yang didisposisikan ke unit petugas ini. */
    public function index(Request $request)
    {
        $pengguna = Auth::user();

        $q      = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');
        $f      = PeriodeFilter::dari($request);

        $query = Aduan::query()
            ->terlihatOleh($pengguna)
            ->with('kategori')
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
                  ->orWhere('judul', 'like', "%{$q}%");
            });
        }

        // Filter tanggal / bulan / tahun (berdasarkan Tanggal Masuk)
        PeriodeFilter::terapkan($query, 'dibuat_pada', $f);

        // Unduh Excel (?export=xlsx) atau cetak PDF (?cetak=1): tabel saja, semua baris sesuai filter
        $info = array_values(array_filter([
            $status !== '' ? 'Status: ' . StatusAduan::from($status)->label() : null,
            $q !== '' ? 'Pencarian: ' . $q : null,
        ]));
        if ($balasan = EksporTiketUnit::jawab($request, $query, 'tiket', 'Tiket Unit Saya', $pengguna->unit->nama ?? '-', $f, $info)) {
            return $balasan;
        }

        $tiket       = $query->paginate(15)->withQueryString();
        $totalSemua  = Aduan::query()->terlihatOleh($pengguna)->count();
        $daftarTahun = PeriodeFilter::daftarTahun(Aduan::query()->terlihatOleh($pengguna), 'dibuat_pada');

        return view('unit.tiket.index', compact('tiket', 'totalSemua', 'q', 'status', 'f', 'daftarTahun'));
    }

    /** Halaman detail tiket + form tanggapan (khusus tiket unit sendiri). */
    public function show(Request $request, Aduan $aduan)
    {
        $pengguna = Auth::user();
        abort_unless($aduan->unit_id && $aduan->unit_id === $pengguna->unit_id, 403);

        $aduan->load(['kategori', 'lokasi', 'unit', 'lampiran', 'riwayat.petugas', 'riwayat.unit']);

        $langkah = $aduan->solusi_dikirim_pada ? 4 : ($aduan->status === StatusAduan::Selesai ? 3 : 2);

        return view('unit.tiket.show', [
            'aduan'   => $aduan,
            'langkah' => $langkah,
        ]);
    }

    /** Petugas unit memperbarui status penanganan tiket unitnya + catatan tanggapan. */
    public function ubahStatus(Request $request, Aduan $aduan)
    {
        $pengguna = Auth::user();
        abort_unless($pengguna->bolehMenanggapi(), 403);
        abort_unless($aduan->unit_id && $aduan->unit_id === $pengguna->unit_id, 403);

        if ($aduan->solusi_dikirim_pada) {
            return back()->with('error', 'Solusi sudah dikirim ke pelapor, status tidak dapat diubah lagi.');
        }

        $data = $request->validate([
            'status'  => ['required', Rule::in(array_column(StatusAduan::cases(), 'value'))],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], PesanValidasi::UMUM, ['status' => 'Status', 'catatan' => 'Catatan']);

        $aduan->ubahStatus(StatusAduan::from($data['status']), $pengguna, $data['catatan'] ?? null);

        return redirect()->route('unit.tickets.show', ['aduan' => $aduan->nomor_tiket])
            ->with('success', 'Tanggapan tersimpan. Status tiket diperbarui.');
    }
}
