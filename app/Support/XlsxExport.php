<?php

namespace App\Support;

use ZipArchive;

/**
 * Unduhan Excel (.xlsx) asli berbentuk tabel: baris judul kolom berwarna, garis kotak,
 * lebar kolom otomatis, baris judul tetap terlihat saat digulir, filter di judul kolom,
 * dan pengaturan cetak landscape satu halaman lebar.
 * Tanpa paket tambahan (memakai ZipArchive bawaan PHP). Bila ZipArchive tidak aktif,
 * otomatis turun ke CSV.
 */
class XlsxExport
{
    private const HEAD = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '';

    private const CT = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';

    private const RELS = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';

    private const WB = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Rekap" sheetId="1" r:id="rId1"/></sheets></workbook>';

    private const WBRELS = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';

    private const STYLES = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n" . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF0F2E5A"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';

    /**
     * @param  array<int, string>  $header  judul kolom
     * @param  iterable<array<int, mixed>>  $baris  isi tabel (angka disimpan sebagai angka, lainnya sebagai teks)
     */
    public static function unduh(string $namaFile, array $header, iterable $baris)
    {
        if (! class_exists(ZipArchive::class)) {
            return CsvExport::unduh(preg_replace('/\.xlsx$/i', '.csv', $namaFile), $header, $baris);
        }

        $jumlahKolom = count($header);
        $lebar       = array_map(fn ($h) => self::lebarTeks((string) $h), $header);

        // 1) Isi tabel ditulis ke berkas sementara (hemat memori untuk data besar)
        $berkasData = tempnam(sys_get_temp_dir(), 'xlsd');
        $out = fopen($berkasData, 'w');
        $n = 1;
        foreach ($baris as $b) {
            $n++;
            fwrite($out, '<row r="' . $n . '">');
            foreach (array_values($b) as $i => $v) {
                if ($i >= $jumlahKolom) {
                    break;
                }
                $v = $v ?? '';
                $lebar[$i] = max($lebar[$i], self::lebarTeks((string) $v));
                fwrite($out, self::sel(self::kolom($i) . $n, 2, $v));
            }
            fwrite($out, '</row>');
        }
        fclose($out);

        // 2) Judul kolom + lebar kolom
        $judul = '';
        foreach (array_values($header) as $i => $h) {
            $judul .= self::sel(self::kolom($i) . '1', 1, (string) $h);
        }
        $kolomXml = '';
        foreach ($lebar as $i => $w) {
            $kolomXml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . min(60, max(6, $w + 2)) . '" customWidth="1"/>';
        }

        // 3) Rakit lembar kerja
        $berkasSheet = tempnam(sys_get_temp_dir(), 'xlss');
        $s = fopen($berkasSheet, 'w');
        fwrite($s, self::HEAD . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/><selection pane="bottomLeft"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="15"/><cols>' . $kolomXml . '</cols><sheetData>'
            . '<row r="1" ht="24" customHeight="1">' . $judul . '</row>');
        $in = fopen($berkasData, 'r');
        stream_copy_to_stream($in, $s);
        fclose($in);
        fwrite($s, '</sheetData><autoFilter ref="A1:' . self::kolom(max(0, $jumlahKolom - 1)) . $n . '"/><pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>');
        fclose($s);

        // 4) Bungkus menjadi berkas .xlsx
        $berkasZip = tempnam(sys_get_temp_dir(), 'xlsz');
        $zip = new ZipArchive();
        $zip->open($berkasZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', self::CT);
        $zip->addFromString('_rels/.rels', self::RELS);
        $zip->addFromString('xl/workbook.xml', self::WB);
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::WBRELS);
        $zip->addFromString('xl/styles.xml', self::STYLES);
        $zip->addFile($berkasSheet, 'xl/worksheets/sheet1.xml');
        $zip->close();

        @unlink($berkasData);
        @unlink($berkasSheet);

        return response()->download($berkasZip, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /** Satu sel: angka disimpan sebagai angka, selain itu sebagai teks. */
    private static function sel(string $ref, int $gaya, mixed $v): string
    {
        if (is_int($v) || is_float($v)) {
            return '<c r="' . $ref . '" s="' . $gaya . '"><v>' . $v . '</v></c>';
        }

        return '<c r="' . $ref . '" s="' . $gaya . '" t="inlineStr"><is><t xml:space="preserve">'
            . self::bersih((string) $v) . '</t></is></c>';
    }

    /** Buang karakter yang dilarang XML lalu ubah &, <, > dan tanda petik. */
    private static function bersih(string $t): string
    {
        $t = mb_scrub($t);
        $t = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $t) ?? '';

        return htmlspecialchars($t, ENT_XML1 | ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /** Panjang baris terpanjang (untuk lebar kolom). */
    private static function lebarTeks(string $t): int
    {
        $maks = 0;
        foreach (explode("\n", $t) as $baris) {
            $maks = max($maks, mb_strlen($baris));
        }

        return $maks;
    }

    /** 0 -> A, 25 -> Z, 26 -> AA */
    private static function kolom(int $i): string
    {
        $huruf = '';
        $i++;
        while ($i > 0) {
            $huruf = chr(65 + (($i - 1) % 26)) . $huruf;
            $i = intdiv($i - 1, 26);
        }

        return $huruf;
    }
}
