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
        Schema::create('aduan', function (Blueprint $table) {
            $table->id()->comment('Nomor urut aduan (internal)');

            // Acak, bukan berurutan, supaya nomor tiket orang lain tidak bisa ditebak lewat "Cek Status".
            $table->string('nomor_tiket', 20)->unique()->comment('Nomor tiket untuk pelapor, contoh: SI-2026-A1B2C3');

            // ── Pelapor ─────────────────────────────────────────
            $table->boolean('anonim')->default(false)->comment('Pelapor anonim? 1 = ya (nama dan WhatsApp tidak diisi)');
            $table->string('nama_pelapor', 100)->nullable()->comment('Nama pelapor (kosong jika anonim)');
            $table->string('no_wa_pelapor', 20)->nullable()->comment('Nomor WhatsApp pelapor, format 62812xxxx (kosong jika anonim)');

            // ── Isi aduan ───────────────────────────────────────
            $table->foreignId('kategori_id')->comment('Kategori aduan')
                ->constrained('kategori')->restrictOnDelete();
            $table->foreignId('lokasi_id')->comment('Lokasi kejadian')
                ->constrained('lokasi')->restrictOnDelete();
            $table->date('tanggal_kejadian')->comment('Tanggal kejadian');
            $table->string('judul', 150)->comment('Judul singkat aduan');
            $table->text('uraian')->comment('Uraian lengkap aduan');
            $table->string('media', 20)->default('portal')->comment('Media aduan masuk: portal, whatsapp, email, kotak_saran');

            // ── Penanganan ──────────────────────────────────────
            $table->string('prioritas', 10)->default('sedang')->comment('Prioritas: tinggi, sedang, rendah (diisi admin)');
            $table->string('status', 20)->default('diproses')->comment('Status aduan: diproses, dikoordinasikan, selesai');

            $table->foreignId('unit_id')->nullable()->comment('Unit yang menangani (kosong sebelum diteruskan)')
                ->constrained('unit')->restrictOnDelete();
            $table->foreignId('diteruskan_oleh')->nullable()->comment('Admin yang meneruskan aduan ke unit')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('diteruskan_pada')->nullable()->comment('Waktu aduan diteruskan ke unit');

            // ── Solusi dan penyelesaian ─────────────────────────
            $table->text('solusi')->nullable()->comment('Solusi atau jawaban untuk pelapor');
            $table->foreignId('diselesaikan_oleh')->nullable()->comment('Petugas yang menyelesaikan aduan')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('solusi_dikirim_pada')->nullable()->comment('Waktu solusi dikirim ke pelapor');
            $table->timestamp('selesai_pada')->nullable()->comment('Waktu aduan selesai (untuk menghitung lama penyelesaian)');

            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu aduan masuk');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
            $table->softDeletes('dihapus_pada')->comment('Waktu aduan dihapus (hapus sementara). Kosong = aduan masih ada');

            // Index untuk daftar aduan dan rekap
            $table->index(['status', 'dibuat_pada']);
            $table->index(['unit_id', 'status']);
            $table->index('media');
            $table->index('tanggal_kejadian');
        });

        $this->komentarTabel('aduan', 'Data aduan dari pasien atau keluarga pasien');
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
