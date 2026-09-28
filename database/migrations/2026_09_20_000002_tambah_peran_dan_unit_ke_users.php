<?php

use App\Support\KomentarTabel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambah kolom ke tabel `users` bawaan Laravel.
 *
 * Tabel `users` (kolom name, email, password) sengaja TIDAK diganti namanya,
 * karena dipakai Laravel dan Filament untuk login. Kolom tambahan memakai bahasa Indonesia.
 * Harus dijalankan SETELAH tabel unit dibuat.
 */
return new class extends Migration
{
    use KomentarTabel;

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Default 'viewer' = hak akses paling kecil, supaya akun baru tidak otomatis berkuasa.
            $table->string('peran', 20)->default('viewer')->after('password')->index()
                ->comment('Peran pengguna: super_admin, operator, petugas_unit, viewer');

            $table->foreignId('unit_id')->nullable()->after('peran')
                ->comment('Unit tempat bertugas (hanya diisi untuk petugas_unit)')
                ->constrained('unit')->nullOnDelete();

            $table->string('no_wa', 20)->nullable()->after('unit_id')
                ->comment('Nomor WhatsApp pengguna, format 62812xxxx');

            $table->boolean('aktif')->default(true)->after('no_wa')
                ->comment('Akun boleh login? 1 = ya, 0 = tidak');
        });

        $this->komentarTabel('users', 'Akun pengguna: super admin, operator, petugas unit, viewer');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
            $table->dropColumn(['peran', 'no_wa', 'aktif']);
        });
    }
};
