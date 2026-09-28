<?php

use App\Support\KodeSingkat;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Format nomor tiket baru: {tanggal}-{kode kategori}-{kode lokasi/poli}-{kode acak}
 * Contoh: 210926-RI-IGD-K7M2 (18 karakter). Kolom nomor_tiket diperpanjang ke 40 agar aman bila kode bertambah panjang.
 * Nomor tiket lama (mis. SI-2026-A1B2C3) tetap berlaku dan tidak diubah.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategori', function (Blueprint $table) {
            $table->string('kode', 6)->nullable()->after('nama')
                ->comment('Kode singkat kategori untuk nomor tiket, contoh: RI');
        });

        Schema::table('lokasi', function (Blueprint $table) {
            $table->string('kode', 6)->nullable()->after('nama')
                ->comment('Kode singkat lokasi atau poli untuk nomor tiket, contoh: IGD');
        });

        // Isi kode untuk data yang sudah ada
        foreach (DB::table('kategori')->get(['id', 'nama']) as $baris) {
            DB::table('kategori')->where('id', $baris->id)->update(['kode' => KodeSingkat::kategori($baris->nama)]);
        }
        foreach (DB::table('lokasi')->get(['id', 'nama']) as $baris) {
            DB::table('lokasi')->where('id', $baris->id)->update(['kode' => KodeSingkat::lokasi($baris->nama)]);
        }

        Schema::table('aduan', function (Blueprint $table) {
            $table->string('nomor_tiket', 40)
                ->comment('Nomor tiket untuk pelapor, contoh: 210926-RI-IGD-K7M2')
                ->change();
        });
    }

    public function down(): void
    {
        // Catatan: mengembalikan kolom ke 20 karakter akan gagal bila sudah ada nomor tiket yang lebih panjang.
        Schema::table('aduan', function (Blueprint $table) {
            $table->string('nomor_tiket', 20)
                ->comment('Nomor tiket untuk pelapor, contoh: SI-2026-A1B2C3')
                ->change();
        });

        Schema::table('lokasi', function (Blueprint $table) {
            $table->dropColumn('kode');
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};
