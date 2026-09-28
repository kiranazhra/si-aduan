<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aduan;

class DashboardController extends Controller
{
    public function index()
    {
        $perStatus = Aduan::query()
            ->selectRaw('status, COUNT(*) AS jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $selesai         = (int) ($perStatus['selesai'] ?? 0);
        $diproses        = (int) ($perStatus['diproses'] ?? 0);
        $dikoordinasikan = (int) ($perStatus['dikoordinasikan'] ?? 0);
        $total           = Aduan::count();

        $terbaru = Aduan::with('kategori')
            ->latest('dibuat_pada')
            ->latest('id')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('total', 'selesai', 'diproses', 'dikoordinasikan', 'terbaru'));
    }
}
