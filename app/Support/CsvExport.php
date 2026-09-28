<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

/** Unduhan CSV yang bisa dibuka langsung di Excel (UTF-8 + BOM). */
class CsvExport
{
    /**
     * @param  array<int, string>  $header
     * @param  iterable<array<int, mixed>>  $baris
     */
    public static function unduh(string $namaFile, array $header, iterable $baris): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $baris) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");   // BOM supaya huruf Indonesia tampil benar di Excel
            fwrite($out, "sep=,\n");         // Excel memakai koma sebagai pemisah kolom
            fputcsv($out, $header);
            foreach ($baris as $b) {
                fputcsv($out, array_map(fn ($v) => $v === null ? '' : (string) $v, $b));
            }
            fclose($out);
        }, $namaFile, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
