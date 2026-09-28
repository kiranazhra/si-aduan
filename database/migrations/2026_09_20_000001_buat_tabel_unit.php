<?php

use App\Support\KomentarTabel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use KomentarTabel;

    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id()->comment('Nomor urut unit');
            $table->string('kode', 10)->unique()->comment('Kode unit, contoh: B0001');
            $table->string('nama', 100)->unique()->comment('Nama unit atau poli, contoh: POLI ANAK');
            $table->string('kelompok', 20)->index()->comment('Kelompok unit: rawat_inap, klinis, poli, penunjang, manajemen, lainnya');
            $table->string('penanggung_jawab', 100)->nullable()->comment('Nama penanggung jawab unit');
            $table->string('no_wa', 20)->nullable()->comment('Nomor WhatsApp penanggung jawab, format 62812xxxx (tanpa + dan spasi)');
            $table->boolean('aktif')->default(true)->comment('Unit masih dipakai? 1 = ya, 0 = tidak (unit tidak dihapus agar riwayat aduan tetap utuh)');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
        });

        $this->komentarTabel('unit', 'Daftar unit dan poli rumah sakit');
    }

    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
