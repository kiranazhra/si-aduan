<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Grading (kolom `prioritas`) sekarang ditentukan Admin saat meneruskan tiket ke unit,
 * bukan diisi otomatis "sedang" untuk semua aduan. Aduan baru berisi NULL = belum digrading.
 *
 * Aduan lama yang BELUM diteruskan ke unit dan masih bernilai 'sedang' hanyalah nilai bawaan
 * (belum pernah dinilai petugas), jadi dikembalikan ke NULL. Aduan yang sudah diteruskan
 * tetap memakai nilainya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->string('prioritas', 10)->nullable()->default(null)
                ->comment('Grading: tinggi = Merah (1x24 jam), sedang = Kuning (3 hari kerja), rendah = Hijau (7 hari kerja). Kosong = belum digrading (diisi Admin saat meneruskan ke unit)')
                ->change();
        });

        DB::table('aduan')
            ->whereNull('unit_id')
            ->where('prioritas', 'sedang')
            ->update(['prioritas' => null]);
    }

    public function down(): void
    {
        DB::table('aduan')->whereNull('prioritas')->update(['prioritas' => 'sedang']);

        Schema::table('aduan', function (Blueprint $table) {
            $table->string('prioritas', 10)->nullable(false)->default('sedang')
                ->comment('Prioritas: tinggi, sedang, rendah (diisi admin)')
                ->change();
        });
    }
};
