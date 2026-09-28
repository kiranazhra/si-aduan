<?php

namespace App\Http\Controllers;

use App\Models\Aduan;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $nomorTiket = $request->query('nomor_tiket');
        $aduan = null;
        $notFound = false;

        if ($nomorTiket) {
            $aduan = Aduan::with('kategori')
                ->where('nomor_tiket', strtoupper(trim($nomorTiket)))
                ->first();

            $notFound = ! $aduan;
        }

        return view('check-status', [
            'nomorTiket' => $nomorTiket,
            'aduan'      => $aduan,
            'notFound'   => $notFound,
        ]);
    }
}
