<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tanggal kejadian sekarang menyimpan tanggal sekaligus jam kejadian. */
    public function up(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dateTime('tanggal_kejadian')->comment('Tanggal dan waktu kejadian')->change();
        });
    }

    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->date('tanggal_kejadian')->comment('Tanggal kejadian')->change();
        });
    }
};
