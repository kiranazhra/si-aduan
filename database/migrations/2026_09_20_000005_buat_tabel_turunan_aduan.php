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
        // ── Lampiran bukti ──────────────────────────────────────
        Schema::create('lampiran_aduan', function (Blueprint $table) {
            $table->id()->comment('Nomor urut lampiran');
            $table->foreignId('aduan_id')->comment('Aduan pemilik lampiran')
                ->constrained('aduan')->cascadeOnDelete();
            $table->string('alamat_file')->comment('Lokasi file di penyimpanan');
            $table->string('nama_file')->comment('Nama file asli');
            $table->string('jenis_file', 100)->comment('Jenis file, contoh: image/jpeg atau application/pdf');
            $table->unsignedInteger('ukuran')->comment('Ukuran file dalam byte');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu file diunggah');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
        });
        $this->komentarTabel('lampiran_aduan', 'Lampiran bukti aduan (gambar atau PDF)');

        // ── Riwayat ─────────────────────────────────────────────
        // Satu sumber data untuk Admin dan Petugas Unit: perubahan dari mana pun
        // (tabel, kartu "Perbarui Status", atau halaman Admin) ditulis di sini.
        Schema::create('riwayat_aduan', function (Blueprint $table) {
            $table->id()->comment('Nomor urut riwayat');
            $table->foreignId('aduan_id')->comment('Aduan yang punya riwayat ini')
                ->constrained('aduan')->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->comment('Petugas yang melakukan (kosong = sistem)')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->comment('Unit yang terkait dengan kejadian ini')
                ->constrained('unit')->nullOnDelete();
            $table->string('jenis', 30)->comment('Jenis kejadian: diterima, diteruskan, status_diubah, tanggapan, solusi_dikirim, selesai');
            $table->string('judul', 200)->comment('Judul singkat kejadian, contoh: Diteruskan ke POLI ANAK');
            $table->text('catatan')->nullable()->comment('Catatan tanggapan petugas (boleh kosong)');
            $table->string('status', 20)->nullable()->comment('Status aduan setelah kejadian ini: diproses, dikoordinasikan, selesai');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu kejadian');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');

            $table->index(['aduan_id', 'dibuat_pada']);
        });
        $this->komentarTabel('riwayat_aduan', 'Riwayat perjalanan setiap aduan');

        // ── Log WhatsApp ────────────────────────────────────────
        Schema::create('pesan_whatsapp', function (Blueprint $table) {
            $table->id()->comment('Nomor urut pesan');
            $table->foreignId('aduan_id')->comment('Aduan yang terkait')
                ->constrained('aduan')->cascadeOnDelete();
            $table->foreignId('dikirim_oleh')->nullable()->comment('Admin yang mengirim pesan')
                ->constrained('users')->nullOnDelete();
            $table->string('jenis_penerima', 10)->comment('Penerima pesan: unit atau pelapor');
            $table->string('no_wa_penerima', 20)->comment('Nomor WhatsApp penerima, format 62812xxxx');
            $table->text('isi_pesan')->comment('Isi pesan');
            $table->string('status', 10)->default('menunggu')->comment('Status kirim: menunggu (belum), dibuka (tautan WhatsApp dibuka), terkirim, gagal');
            $table->text('keterangan_gagal')->nullable()->comment('Keterangan bila pengiriman gagal');
            $table->timestamp('terkirim_pada')->nullable()->comment('Waktu pesan terkirim');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');

            $table->index(['aduan_id', 'jenis_penerima']);
        });
        $this->komentarTabel('pesan_whatsapp', 'Catatan pesan WhatsApp ke unit dan ke pelapor');

        // ── Pengaturan ──────────────────────────────────────────
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id()->comment('Nomor urut pengaturan');
            $table->string('nama', 50)->unique()->comment('Nama pengaturan, contoh: notifikasi_otomatis');
            $table->text('isi')->nullable()->comment('Isi pengaturan (1 = aktif, 0 = nonaktif, atau angka/teks)');
            $table->timestamp('dibuat_pada')->nullable()->comment('Waktu data dibuat');
            $table->timestamp('diubah_pada')->nullable()->comment('Waktu data terakhir diubah');
        });
        $this->komentarTabel('pengaturan', 'Pengaturan aplikasi (menu Konfigurasi)');
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
        Schema::dropIfExists('pesan_whatsapp');
        Schema::dropIfExists('riwayat_aduan');
        Schema::dropIfExists('lampiran_aduan');
    }
};
