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
        Schema::create('kategori', function (Blueprint $table) {
            $table->id()->comment('Nomor urut kategori');
            $table->string('nama', 100)->unique()->comment('Nama kategori aduan, contoh: Farmasi & Obat');
            $table->unsignedSmallInteger('urutan')->default(0)->comment('Urutan tampil di form aduan');
            $table->boolean('aktif')->default(true)->comment('Tampil di form? 1 = ya, 0 = tidak');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
        });
        $this->komentarTabel('kategori', 'Kategori aduan (pilihan Kategori di form)');

        Schema::create('lokasi', function (Blueprint $table) {
            $table->id()->comment('Nomor urut lokasi');
            $table->string('nama', 100)->unique()->comment('Nama lokasi kejadian, contoh: IGD');
            $table->unsignedSmallInteger('urutan')->default(0)->comment('Urutan tampil di form aduan');
            $table->boolean('aktif')->default(true)->comment('Tampil di form? 1 = ya, 0 = tidak');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
        });
        $this->komentarTabel('lokasi', 'Lokasi kejadian (pilihan Lokasi Kejadian di form)');
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
        Schema::dropIfExists('kategori');
    }
};
