<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Menambah alamat pelapor (opsional) ke tabel `aduan`. Data aduan yang sudah ada tidak berubah. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->string('alamat_pelapor', 255)->nullable()->after('no_wa_pelapor')
                ->comment('Alamat pelapor (opsional, kosong bila anonim)');
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn('alamat_pelapor');
        });
    }
};
