<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /** Halaman "Profil Saya" — data akun petugas unit yang sedang login. */
    public function index()
    {
        return view('unit.profil', ['pengguna' => Auth::user()]);
    }
}
