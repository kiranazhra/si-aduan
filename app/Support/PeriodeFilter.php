<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Filter tanggal / bulan / tahun untuk daftar tiket.
 * Parameter URL: ?tanggal=2026-09-21  ?bulan=9  ?tahun=2026
 * Aturan: tanggal (harian) paling kuat dan mengabaikan bulan & tahun.
 *         bulan + tahun = satu bulan tertentu; tahun saja = setahun; bulan saja = bulan itu di semua tahun.
 */
class PeriodeFilter
{
    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /** @return array{tanggal: ?string, bulan: ?int, tahun: ?int} */
    public static function dari(Request $request): array
    {
        $tanggal = (string) $request->query('tanggal', '');
        $tanggalValid = preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $m)
            && checkdate((int) $m[2], (int) $m[3], (int) $m[1]);

        $bulan = (int) $request->query('bulan', 0);
        $tahun = (int) $request->query('tahun', 0);

        return [
            'tanggal' => $tanggalValid ? $tanggal : null,
            'bulan'   => ($bulan >= 1 && $bulan <= 12) ? $bulan : null,
            'tahun'   => ($tahun >= 2000 && $tahun <= 2100) ? $tahun : null,
        ];
    }

    /** Terapkan filter ke query, pada kolom tanggal/waktu $kolom. */
    public static function terapkan(Builder $query, string $kolom, array $f): Builder
    {
        if ($f['tanggal']) {
            $hari = Carbon::createFromFormat('Y-m-d', $f['tanggal']);

            return $query->whereBetween($kolom, [$hari->copy()->startOfDay(), $hari->copy()->endOfDay()]);
        }

        if ($f['tahun'] && $f['bulan']) {
            $awal = Carbon::create($f['tahun'], $f['bulan'], 1, 0, 0, 0);

            return $query->whereBetween($kolom, [$awal, $awal->copy()->endOfMonth()]);
        }

        if ($f['tahun']) {
            $awal = Carbon::create($f['tahun'], 1, 1, 0, 0, 0);

            return $query->whereBetween($kolom, [$awal, $awal->copy()->endOfYear()]);
        }

        if ($f['bulan']) {
            return $query->whereMonth($kolom, $f['bulan']);
        }

        return $query;
    }

    /** Teks periode untuk judul cetakan, contoh: "September 2026". */
    public static function label(array $f): string
    {
        if ($f['tanggal']) {
            return Carbon::createFromFormat('Y-m-d', $f['tanggal'])->locale('id')->translatedFormat('j F Y');
        }
        if ($f['tahun'] && $f['bulan']) {
            return self::BULAN[$f['bulan']] . ' ' . $f['tahun'];
        }
        if ($f['tahun']) {
            return 'Tahun ' . $f['tahun'];
        }
        if ($f['bulan']) {
            return 'Bulan ' . self::BULAN[$f['bulan']] . ' (semua tahun)';
        }

        return 'Semua periode';
    }

    public static function aktif(array $f): bool
    {
        return (bool) ($f['tanggal'] || $f['bulan'] || $f['tahun']);
    }

    /**
     * Daftar tahun untuk pilihan filter: tahun-tahun yang punya data + tahun berjalan.
     * Beri query awal yang sudah dibatasi (mis. hanya tiket milik unit ini).
     *
     * @return array<int, int>
     */
    public static function daftarTahun(Builder $query, string $kolom): array
    {
        return $query->reorder()
            ->whereNotNull($kolom)
            ->selectRaw("DISTINCT YEAR({$kolom}) AS t")
            ->pluck('t')
            ->push(now()->year)
            ->map(fn ($t) => (int) $t)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }
}
