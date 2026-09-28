<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Menambah penilaian pelayanan (1-5 bintang) dari pelapor ke tabel `aduan`. Data lama tetap kosong (NULL). */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->after('uraian')
                ->comment('Penilaian pelayanan dari pelapor: 5 = sangat puas, 4 = puas, 3 = cukup puas, 2 = kurang puas, 1 = tidak puas');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropIndex(['rating']);
            $table->dropColumn('rating');
        });
    }
};
