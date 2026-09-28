<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Aduan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $pengguna = Auth::user();

        $dasar = Aduan::query()->terlihatOleh($pengguna);

        $total           = (clone $dasar)->count();
        $diproses        = (clone $dasar)->where('status', 'diproses')->count();
        $dikoordinasikan = (clone $dasar)->where('status', 'dikoordinasikan')->count();
        $selesai         = (clone $dasar)->where('status', 'selesai')->count();

        $terbaru = (clone $dasar)
            ->with('kategori')
            ->where('status', '!=', 'selesai')
            ->latest('dibuat_pada')
            ->latest('id')
            ->take(5)
            ->get();

        // Penilaian pelayanan (bintang) untuk aduan unit ini
        $ratingDb = (clone $dasar)->whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) AS jumlah')->groupBy('rating')->pluck('jumlah', 'rating');

        $jumlahRating = (int) $ratingDb->sum();
        $jumlahBintang = 0;
        foreach ($ratingDb as $bintang => $jml) {
            $jumlahBintang += (int) $bintang * (int) $jml;
        }
        $rataRating = $jumlahRating ? round($jumlahBintang / $jumlahRating, 1) : null;

        $sebaranRating = collect([5, 4, 3, 2, 1])->map(fn (int $n) => (object) [
            'nilai'  => $n,
            'label'  => Aduan::RATING_LABEL[$n],
            'jumlah' => (int) ($ratingDb[$n] ?? 0),
            'pct'    => $jumlahRating ? (int) round(($ratingDb[$n] ?? 0) / $jumlahRating * 100) : 0,
        ]);

        return view('unit.dashboard', compact(
            'total', 'diproses', 'dikoordinasikan', 'selesai', 'terbaru',
            'jumlahRating', 'rataRating', 'sebaranRating'
        ));
    }
}
