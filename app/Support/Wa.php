<?php

namespace App\Support;

use App\Models\Aduan;

/**
 * Bantuan untuk WhatsApp (tautan wa.me) dan isi pesan yang dipakai di halaman admin.
 */
class Wa
{
    /** Ubah 0812-3456-7890 / +62 812 3456 7890 menjadi 6281234567890. */
    public static function normalisasi(?string $nomor): ?string
    {
        $angka = preg_replace('/\D+/', '', (string) $nomor);

        if ($angka === '') {
            return null;
        }
        if (str_starts_with($angka, '62')) {
            return $angka;
        }
        if (str_starts_with($angka, '0')) {
            return '62' . substr($angka, 1);
        }
        if (str_starts_with($angka, '8')) {
            return '62' . $angka;
        }

        return $angka;
    }

    /** Tautan wa.me. Bila nomor kosong, WhatsApp akan meminta pengguna memilih kontak. */
    public static function url(?string $nomor, string $teks = ''): string
    {
        return 'https://wa.me/' . (self::normalisasi($nomor) ?? '') . '?text=' . rawurlencode($teks);
    }

    /** Pesan disposisi ke unit (sama dengan prototype). */
    public static function pesanDisposisi(Aduan $aduan): string
    {
        $ringkas = mb_strlen($aduan->uraian) > 160
            ? mb_substr($aduan->uraian, 0, 160) . '…'
            : $aduan->uraian;

        return "[SI-ADUAN] Disposisi Tiket Pengaduan\n\n"
            . "No. Tiket   : {$aduan->nomor_tiket}\n"
            . 'Kategori    : ' . ($aduan->kategori->nama ?? '-') . "\n"
            . 'Lokasi      : ' . ($aduan->lokasi->nama ?? '-') . "\n"
            . 'Tanggal     : ' . $aduan->tanggal_kejadian->locale('id')->translatedFormat('d F Y · H.i') . "\n\n"
            . "Isi Aduan:\n{$ringkas}\n\n"
            . "Mohon ditindaklanjuti sesuai prosedur yang berlaku.\n\n"
            . "Salam,\nAdmin RSUD H. Damanhuri Barabai";
    }

    /** Pesan solusi ke pelapor. Beri $solusi = '__SOLUSI__' untuk membuat templat pratinjau. */
    public static function pesanSolusi(Aduan $aduan, string $solusi): string
    {
        $nama = $aduan->anonim || ! $aduan->nama_pelapor ? 'Pelapor' : $aduan->nama_pelapor;

        return "Yth. Bpk/Ibu {$nama},\n\n"
            . "Terima kasih telah menyampaikan pengaduan Anda ke RSUD H. Damanhuri Barabai.\n\n"
            . "Nomor tiket: {$aduan->nomor_tiket}\n\n"
            . "{$solusi}\n\n"
            . "Hormat kami,\nTim Humas RSUD H. Damanhuri Barabai";
    }
}
