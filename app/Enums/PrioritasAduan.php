<?php

namespace App\Enums;

use Carbon\CarbonInterface;

/**
 * Grading aduan sesuai standar pelayanan, ditentukan Admin saat meneruskan tiket ke unit.
 *
 *  - Merah  (berat/mendesak) : 1 x 24 jam
 *  - Kuning (sedang)         : 3 hari kerja
 *  - Hijau  (ringan)         : 7 hari kerja
 *
 * Nilai di database tetap 'tinggi' / 'sedang' / 'rendah' supaya data lama tidak perlu diubah.
 * Hari kerja dihitung Senin-Sabtu, Minggu dilewati (hari libur nasional belum diperhitungkan).
 */
enum PrioritasAduan: string
{
    case Tinggi = 'tinggi';
    case Sedang = 'sedang';
    case Rendah = 'rendah';

    /** Nama grading: Merah / Kuning / Hijau. */
    public function label(): string
    {
        return match ($this) {
            self::Tinggi => 'Merah',
            self::Sedang => 'Kuning',
            self::Rendah => 'Hijau',
        };
    }

    /** Keterangan singkat jenis pengaduan. */
    public function keterangan(): string
    {
        return match ($this) {
            self::Tinggi => 'Berat / mendesak',
            self::Sedang => 'Sedang',
            self::Rendah => 'Ringan',
        };
    }

    /** Batas waktu penanganan dalam tulisan. */
    public function waktuLabel(): string
    {
        return match ($this) {
            self::Tinggi => '1 × 24 Jam',
            self::Sedang => '3 Hari Kerja',
            self::Rendah => '7 Hari Kerja',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Tinggi => 'danger',
            self::Sedang => 'warning',
            self::Rendah => 'success',
        };
    }

    /** Batas akhir penanganan, dihitung sejak aduan diterima. */
    public function batasWaktu(CarbonInterface $diterima): CarbonInterface
    {
        return match ($this) {
            self::Tinggi => $diterima->copy()->addHours(24),
            self::Sedang => self::tambahHariKerja($diterima, 3),
            self::Rendah => self::tambahHariKerja($diterima, 7),
        };
    }

    /** Tambah $jumlah hari kerja (Senin-Sabtu) ke tanggal $dari; Minggu dilewati. */
    private static function tambahHariKerja(CarbonInterface $dari, int $jumlah): CarbonInterface
    {
        $tanggal = $dari->copy();

        while ($jumlah > 0) {
            $tanggal = $tanggal->addDay();

            if (! $tanggal->isSunday()) {
                $jumlah--;
            }
        }

        return $tanggal;
    }

    /** Pilihan untuk form Admin, urut dari yang paling mendesak. */
    public static function pilihan(): array
    {
        return [self::Tinggi, self::Sedang, self::Rendah];
    }
}
