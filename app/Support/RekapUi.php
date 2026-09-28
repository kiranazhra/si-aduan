<?php

namespace App\Support;

use Illuminate\Support\Js;

class RekapUi
{
    /**
     * Isi atribut @click untuk membuka jendela "Detail" di halaman rekap.
     * Ikut membawa filter Bulan/Tanggal/Unit yang sedang aktif di halaman,
     * supaya isi jendela detail konsisten dengan filter yang dipilih pengguna.
     *
     * Contoh: <button @click="{{ RekapUi::detail('Selesai', 'status', 'selesai', $tahun, $bulan, $tanggal, $unitId) }}">
     */
    public static function detail(
        string $judul,
        string $by,
        string|int|null $nilai,
        int $tahun,
        ?int $bulan = null,
        ?string $tanggal = null,
        ?int $unitId = null,
    ): string {
        $param = ['tahun' => $tahun, 'by' => $by];

        if ($nilai !== null) {
            $param['nilai'] = $nilai;
        }
        if ($bulan !== null) {
            $param['bulan'] = $bulan;
        }
        if ($tanggal !== null) {
            $param['tanggal'] = $tanggal;
        }
        if ($unitId !== null) {
            $param['unit_id'] = $unitId;
        }

        return "\$dispatch('buka-detail', " . Js::from([
            'judul' => $judul,
            'url'   => route('admin.recap.detail', $param),
        ]) . ')';
    }
}
