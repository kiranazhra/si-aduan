<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    /** Halaman "Profil Saya" — data akun Super Admin/Operator/Viewer yang sedang login. */
    public function index()
    {
        return view('admin.profil', ['pengguna' => Auth::user()]);
    }
}
