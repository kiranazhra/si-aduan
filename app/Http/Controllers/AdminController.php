<?php

namespace App\Http\Controllers;

use App\Enums\StatusAduan;
use App\Models\Aduan;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Dashboard admin (super_admin, operator, viewer).
     * Petugas unit tidak lewat sini — lihat UnitController (batch berikutnya).
     */
    public function dashboard(): View
    {
        $totalAduan      = Aduan::count();
        $selesai         = Aduan::where('status', StatusAduan::Selesai)->count();
        $diproses        = Aduan::where('status', StatusAduan::Diproses)->count();
        $dikoordinasikan = Aduan::where('status', StatusAduan::Dikoordinasikan)->count();

        $stats = [
            ['label' => 'Total Pengaduan', 'value' => $totalAduan, 'icon' => 'inbox', 'color' => 'bg-blue-50 text-blue-600'],
            ['label' => 'Selesai', 'value' => $selesai, 'icon' => 'check_circle', 'color' => 'bg-emerald-50 text-emerald-600'],
            ['label' => 'Diproses', 'value' => $diproses, 'icon' => 'autorenew', 'color' => 'bg-amber-50 text-amber-600'],
            ['label' => 'Dikoordinasikan', 'value' => $dikoordinasikan, 'icon' => 'swap_horiz', 'color' => 'bg-blue-50 text-blue-600'],
        ];

        $pieData = [
            ['name' => 'Selesai', 'value' => $selesai, 'color' => '#059669'],
            ['name' => 'Diproses', 'value' => $diproses, 'color' => '#f59e0b'],
            ['name' => 'Dikoordinasikan', 'value' => $dikoordinasikan, 'color' => '#3b82f6'],
        ];

        $tiketTerbaru = Aduan::with('kategori')
            ->latest('dibuat_pada')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pieData', 'tiketTerbaru', 'totalAduan'));
    }
}
