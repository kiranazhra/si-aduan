<?php

namespace App\Http\Controllers;

use App\Models\Aduan;

class HomeController extends Controller
{
    public function index()
    {
        // Sesuaikan dengan model Aduan yang sudah kamu salin di app/Models
        $totalAduan   = Aduan::count();
        $totalSelesai = Aduan::where('status', 'selesai')->count();
        $rataRespons  = 2; // Bisa dihitung dari selisih dibuat_pada & selesai_pada kalau perlu

        return view('home', compact('totalAduan', 'totalSelesai', 'rataRespons'));
    }
}
