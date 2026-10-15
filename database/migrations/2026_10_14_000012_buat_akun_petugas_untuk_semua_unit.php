<?php

use App\Support\AkunUnit;
use Illuminate\Database\Migrations\Migration;

/**
 * Membuat akun Petugas Unit untuk semua unit yang sudah ada (yang belum punya).
 * Unit baru berikutnya otomatis dibuatkan lewat Unit::booted().
 */
return new class extends Migration
{
    public function up(): void
    {
        AkunUnit::buatSemua();
    }

    public function down(): void
    {
        // Akun tidak dihapus otomatis agar tidak menghilangkan akun yang sudah dipakai.
    }
};
