<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

/** Data awal dari form pengaduan dan halaman Konfigurasi di prototype. */
class DataAwalSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Pelayanan Rawat Inap', 'Pelayanan Rawat Jalan', 'IGD & Gawat Darurat',
            'Administrasi & Pendaftaran', 'Fasilitas & Kebersihan',
            'Tenaga Medis & Perawat', 'Farmasi & Obat', 'Lainnya',
        ];

        $lokasi = [
            'Rawat Inap Lt. 1', 'Rawat Inap Lt. 2', 'Rawat Jalan', 'IGD', 'Farmasi',
            'Pendaftaran', 'Laboratorium', 'Radiologi', 'Area Parkir & Umum',
        ];

        foreach ($kategori as $i => $nama) {
            Kategori::firstOrCreate(['nama' => $nama], ['urutan' => $i + 1]);
        }

        foreach ($lokasi as $i => $nama) {
            Lokasi::firstOrCreate(['nama' => $nama], ['urutan' => $i + 1]);
        }

        // Tombol di halaman Konfigurasi (nilai awal sama dengan prototype)
        foreach ([
            'notifikasi_otomatis'   => '1', // kirim WhatsApp ke pelapor saat status berubah
            'tutup_otomatis'        => '0', // tutup aduan otomatis bila tanpa respons
            'tutup_otomatis_hari'   => '7', // berapa hari tanpa respons
            'peringatan_prioritas'  => '1', // peringatan WhatsApp ke admin untuk aduan prioritas Tinggi
        ] as $nama => $isi) {
            Pengaturan::firstOrCreate(['nama' => $nama], ['isi' => $isi]);
        }
    }
}
