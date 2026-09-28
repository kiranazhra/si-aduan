<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use App\Support\EksporTiketUnit;
use App\Support\PeriodeFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /** Halaman "Riwayat Selesai" — aduan unit ini yang sudah berstatus Selesai. */
    public function index(Request $request)
    {
        $pengguna = Auth::user();
        $f        = PeriodeFilter::dari($request);

        $query = Aduan::query()
            ->terlihatOleh($pengguna)
            ->where('status', 'selesai')
            ->with('kategori')
            ->latest('selesai_pada')
            ->latest('id');

        // Filter tanggal / bulan / tahun (berdasarkan Tanggal Selesai)
        PeriodeFilter::terapkan($query, 'selesai_pada', $f);

        // Unduh Excel (?export=xlsx) atau cetak PDF (?cetak=1): tabel saja, semua baris sesuai filter
        if ($balasan = EksporTiketUnit::jawab($request, $query, 'riwayat', 'Riwayat Selesai', $pengguna->unit->nama ?? '-', $f)) {
            return $balasan;
        }

        $selesai     = $query->paginate(15)->withQueryString();
        $daftarTahun = PeriodeFilter::daftarTahun(
            Aduan::query()->terlihatOleh($pengguna)->where('status', 'selesai'),
            'selesai_pada'
        );

        return view('unit.riwayat', compact('selesai', 'f', 'daftarTahun'));
    }
}
