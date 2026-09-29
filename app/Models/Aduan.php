<?php

namespace App\Models;

use App\Enums\JenisRiwayat;
use App\Enums\MediaAduan;
use App\Enums\PrioritasAduan;
use App\Enums\StatusAduan;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Aduan extends Model
{
    use SoftDeletes;

    protected $table = 'aduan';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diubah_pada';
    const DELETED_AT = 'dihapus_pada';

    /** Arti nilai bintang penilaian pelayanan. */
    public const RATING_LABEL = [
        5 => 'Sangat Puas',
        4 => 'Puas',
        3 => 'Cukup Puas',
        2 => 'Kurang Puas',
        1 => 'Tidak Puas',
    ];

    /** Nilai awal aduan baru (sama dengan default di database). */
    protected $attributes = [
        'status' => 'diproses',
        'media'  => 'portal',
        'anonim' => false,
    ];

    protected $fillable = [
        'nomor_tiket', 'anonim', 'nama_pelapor', 'no_wa_pelapor', 'alamat_pelapor',
        'kategori_id', 'lokasi_id', 'tanggal_kejadian', 'judul', 'uraian', 'rating', 'media',
        'prioritas', 'status', 'unit_id', 'diteruskan_oleh', 'diteruskan_pada',
        'solusi', 'diselesaikan_oleh', 'solusi_dikirim_pada', 'selesai_pada',
    ];

    protected function casts(): array
    {
        return [
            'anonim'              => 'boolean',
            'rating'              => 'integer',
            'tanggal_kejadian'    => 'datetime',
            'media'               => MediaAduan::class,
            'prioritas'           => PrioritasAduan::class,
            'status'              => StatusAduan::class,
            'diteruskan_pada'     => 'datetime',
            'solusi_dikirim_pada' => 'datetime',
            'selesai_pada'        => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Aduan $aduan) {
            $aduan->nomor_tiket ??= static::buatNomorTiket($aduan->kategori_id, $aduan->lokasi_id);
        });

        // Setiap aduan baru otomatis punya baris pertama di riwayat.
        static::created(function (Aduan $aduan) {
            $aduan->catat(JenisRiwayat::Diterima, 'Aduan diterima sistem');
        });
    }

    /**
     * Nomor tiket: {tanggal}-{kode kategori}-{kode lokasi/poli}-{kode acak}
     * Contoh: 210926-RI-IGD-K7M2
     *
     *  - tanggal     : hari, bulan, tahun 2 angka saat aduan dibuat (ddmmyy)
     *  - kategori    : 2 huruf, dari tabel kategori (kolom kode)
     *  - lokasi/poli : 3 huruf, dari tabel unit (pilihan pelapor), dibuat dari nama unit
     *  - kode acak   : 4 karakter tanpa huruf/angka yang mirip (0/O, 1/I), supaya nomor
     *                  orang lain tidak mudah ditebak lewat halaman Cek Status.
     */
    public static function buatNomorTiket(?int $kategoriId = null, ?int $lokasiId = null): string
    {
        $tanggal  = now()->format('dmy');
        $kategori = $kategoriId ? Kategori::find($kategoriId)?->kodeTiket() : null;
        $lokasi   = $lokasiId ? Unit::find($lokasiId)?->kodeTiket() : null;

        $awal  = $tanggal . '-' . ($kategori ?: 'XX') . '-' . ($lokasi ?: 'XXX') . '-';
        $abjad = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maks  = strlen($abjad) - 1;

        do {
            $acak = '';
            for ($i = 0; $i < 4; $i++) {
                $acak .= $abjad[random_int(0, $maks)];
            }
            $nomor = $awal . $acak;
        } while (static::withTrashed()->where('nomor_tiket', $nomor)->exists());

        return $nomor;
    }

    // ── Relasi ─────────────────────────────────────────────
    public function kategori(): BelongsTo { return $this->belongsTo(Kategori::class); }

    /**
     * Unit/poli tempat kejadian (pilihan pelapor di form). Kolom foreign key-nya
     * masih bernama `lokasi_id` (tidak diubah agar riwayat lama tidak perlu migrasi
     * data), tapi sekarang menunjuk ke tabel `unit`, bukan tabel `lokasi` lagi.
     */
    public function lokasi(): BelongsTo   { return $this->belongsTo(Unit::class, 'lokasi_id'); }

    /** Unit yang menangani/diteruskan aduan ini (diisi admin, beda dari lokasi kejadian). */
    public function unit(): BelongsTo     { return $this->belongsTo(Unit::class); }

    public function diteruskanOleh(): BelongsTo   { return $this->belongsTo(User::class, 'diteruskan_oleh'); }
    public function diselesaikanOleh(): BelongsTo { return $this->belongsTo(User::class, 'diselesaikan_oleh'); }

    public function lampiran(): HasMany
    {
        return $this->hasMany(LampiranAduan::class);
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatAduan::class)->orderBy('dibuat_pada')->orderBy('id');
    }

    public function pesanWhatsapp(): HasMany
    {
        return $this->hasMany(PesanWhatsapp::class);
    }

    // ── Scope ──────────────────────────────────────────────
    /** Petugas unit hanya melihat aduan unitnya; peran lain melihat semua. */
    public function scopeTerlihatOleh(Builder $query, User $pengguna): Builder
    {
        if (! $pengguna->bolehMenanggapi()) {
            return $query;
        }

        // Petugas tanpa unit tidak boleh melihat apa pun (jangan sampai cocok dengan unit_id NULL).
        return $pengguna->unit_id
            ? $query->where('unit_id', $pengguna->unit_id)
            : $query->whereRaw('1 = 0');
    }

    // ── Aksi (dipakai bersama oleh Admin dan Petugas Unit) ─
    /** Catat satu baris di riwayat aduan, dengan status aduan saat ini. */
    public function catat(JenisRiwayat $jenis, string $judul, ?User $oleh = null, ?string $catatan = null, ?int $unitId = null): RiwayatAduan
    {
        return $this->riwayat()->create([
            'petugas_id' => $oleh?->id,
            'unit_id'    => $unitId ?? $oleh?->unit_id,
            'jenis'      => $jenis,
            'judul'      => $judul,
            'catatan'    => $catatan,
            'status'     => $this->status,
        ]);
    }

    /**
     * Satu-satunya pintu untuk mengubah status + mencatat riwayat, supaya data
     * yang dilihat Admin dan Petugas Unit selalu sama (satu sumber data).
     */
    public function ubahStatus(StatusAduan $status, User $oleh, ?string $catatan = null): void
    {
        $this->update([
            'status'       => $status,
            'selesai_pada' => $status === StatusAduan::Selesai ? ($this->selesai_pada ?? now()) : null,
        ]);

        $catatan
            ? $this->catat(JenisRiwayat::Tanggapan, "Tanggapan dari {$oleh->name}", $oleh, $catatan)
            : $this->catat(JenisRiwayat::StatusDiubah, "Status diubah menjadi {$status->label()}", $oleh);
    }

    /**
     * Teruskan ke unit -> status otomatis 'Dikoordinasikan'.
     * Admin sekaligus menentukan grading (Merah/Kuning/Hijau) yang menjadi dasar batas waktu penanganan.
     */
    public function teruskanKe(Unit $unit, User $oleh, PrioritasAduan $grading): void
    {
        $this->update([
            'unit_id'         => $unit->id,
            'prioritas'       => $grading,
            'diteruskan_oleh' => $oleh->id,
            'diteruskan_pada' => now(),
            'status'          => StatusAduan::Dikoordinasikan,
        ]);

        $this->catat(
            JenisRiwayat::Diteruskan,
            "Diteruskan ke {$unit->nama} · Grading {$grading->label()} ({$grading->waktuLabel()})",
            $oleh,
            null,
            $unit->id,
        );
    }

    /** Batas akhir penanganan sesuai grading, dihitung sejak aduan diterima (null bila belum digrading). */
    public function batasWaktu(): ?CarbonInterface
    {
        return ($this->prioritas instanceof PrioritasAduan && $this->dibuat_pada)
            ? $this->prioritas->batasWaktu($this->dibuat_pada)
            : null;
    }

    /** Sudah lewat batas waktu? Tiket selesai dinilai dari waktu selesainya, tiket berjalan dari waktu sekarang. */
    public function melewatiBatas(): bool
    {
        $batas = $this->batasWaktu();

        return $batas !== null && ($this->selesai_pada ?? now())->greaterThan($batas);
    }

    /** Tulisan penilaian, contoh: "Sangat Puas" (null bila pelapor tidak memberi penilaian). */
    public function labelRating(): ?string
    {
        return $this->rating ? (self::RATING_LABEL[$this->rating] ?? null) : null;
    }

    /** Lama penyelesaian dalam hari (null bila belum selesai). */
    public function lamaPenyelesaianHari(): ?int
    {
        return $this->selesai_pada
            ? (int) $this->dibuat_pada->diffInDays($this->selesai_pada)
            : null;
    }
}
