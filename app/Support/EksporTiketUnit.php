<?php

namespace App\Support;

use App\Enums\StatusAduan;
use BackedEnum;
use Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Isi tabel untuk unduhan Excel dan cetakan PDF di portal unit.
 * Isinya sama dengan tabel di layar (tanpa kolom Aksi), ditambah kolom No.
 */
class EksporTiketUnit
{
    /** @param 'tiket'|'riwayat' $jenis */
    public static function header(string $jenis): array
    {
        return $jenis === 'riwayat'
            ? ['No', 'No. Tiket', 'Ringkasan Aduan', 'Kategori', 'Grading', 'Tanggal Masuk', 'Tanggal Selesai']
            : ['No', 'No. Tiket', 'Ringkasan Aduan', 'Kategori', 'Grading', 'Tanggal Masuk', 'Status'];
    }

    /**
     * @param  iterable<\App\Models\Aduan>  $tiket
     * @param  'tiket'|'riwayat'  $jenis
     */
    public static function baris(iterable $tiket, string $jenis): Generator
    {
        $no = 0;
        foreach ($tiket as $t) {
            $no++;

            yield [
                $no,
                $t->nomor_tiket,
                $t->judul,
                $t->kategori->nama ?? '-',
                ucfirst(self::nilai($t->prioritas)),
                $t->dibuat_pada?->locale('id')->translatedFormat('d M Y') ?? '-',
                $jenis === 'riwayat'
                    ? ($t->selesai_pada?->locale('id')->translatedFormat('d M Y') ?? '-')
                    : (StatusAduan::tryFrom(self::nilai($t->status))?->label() ?? ucfirst(self::nilai($t->status))),
            ];
        }
    }

    /**
     * Bila permintaan meminta ?export=xlsx atau ?cetak=1, kembalikan unduhan Excel / halaman cetak
     * (tabel saja, semua baris sesuai filter). Selain itu kembalikan null dan halaman biasa ditampilkan.
     *
     * @param  'tiket'|'riwayat'  $jenis
     * @param  array<int, string>  $info  keterangan filter tambahan untuk kepala cetakan
     */
    public static function jawab(Request $request, Builder $query, string $jenis, string $judul, string $unit, array $f, array $info = [])
    {
        if ($request->query('export') === 'xlsx') {
            $nama = ($jenis === 'riwayat' ? 'riwayat-selesai-' : 'tiket-unit-') . Str::slug($unit) . '-' . now()->format('Ymd') . '.xlsx';

            return XlsxExport::unduh($nama, self::header($jenis), self::baris($query->lazy(), $jenis));
        }

        if ($request->has('cetak')) {
            $batas = 2000;
            $total = (clone $query)->count();

            return view('unit.cetak', [
                'judul'   => $judul,
                'unit'    => $unit,
                'periode' => PeriodeFilter::label($f),
                'info'    => $info,
                'header'  => self::header($jenis),
                'baris'   => iterator_to_array(self::baris($query->limit($batas)->get(), $jenis), false),
                'total'   => $total,
                'batas'   => $batas,
            ]);
        }

        return null;
    }

    /** Nilai kolom yang bisa berupa enum atau teks biasa. */
    private static function nilai(mixed $v): string
    {
        return $v instanceof BackedEnum ? (string) $v->value : (string) $v;
    }
}
