<?php

namespace Database\Seeders;

use App\Enums\PeranPengguna;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun DEMO (sama dengan prototype). Hanya dijalankan di environment 'local'
 * (lihat DatabaseSeeder). JANGAN dipakai di server produksi.
 */
class AkunDemoSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            // [nama, email, password, peran, kode unit]
            ['Admin Utama',              'admin@rsud-damanhuri.id',  'admin123',  PeranPengguna::SuperAdmin, null],
            ['Petugas Humas',            'humas@rsud-damanhuri.id',  'humas123',  PeranPengguna::Operator,   null],
            ['Kepala Bidang Pelayanan',  'viewer@rsud-damanhuri.id', 'viewer123', PeranPengguna::Viewer,     null],

            // Petugas unit (password demo: unit123)
            ['Ns. Siti Aminah, S.Kep',   'al-afiat-lt1@rsud-damanhuri.id',      'unit123', PeranPengguna::PetugasUnit, 'B0004'], // AL - AFIAT LT.1
            ['dr. Hendra Wijaya, Sp.PD', 'rawat-jalan@rsud-damanhuri.id',       'unit123', PeranPengguna::PetugasUnit, 'B0097'], // RAWAT JALAN
            ['apt. Dewi Kurnia, S.Farm', 'instalasi-farmasi@rsud-damanhuri.id', 'unit123', PeranPengguna::PetugasUnit, 'B0098'], // INSTALASI FARMASI
            ['dr. Rizal Fahmi, Sp.A',    'poli-anak@rsud-damanhuri.id',         'unit123', PeranPengguna::PetugasUnit, 'B0022'], // POLI ANAK
            ['dr. Nurul Hidayah',        'ibnu-sina-igd@rsud-damanhuri.id',     'unit123', PeranPengguna::PetugasUnit, 'B0054'], // IBNU SINA (IGD)
        ];

        foreach ($akun as [$nama, $email, $password, $peran, $kodeUnit]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => $nama,
                    'password' => $password, // di-hash otomatis oleh cast 'hashed'
                    'peran'    => $peran,
                    'unit_id'  => $kodeUnit ? Unit::where('kode', $kodeUnit)->value('id') : null,
                    'aktif'    => true,
                ]
            );
        }
    }
}
