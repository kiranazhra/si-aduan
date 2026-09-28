<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Kode singkat kategori dan lokasi/poli untuk nomor tiket.
 * Contoh nomor tiket: 210926-RI-IGD-K7M2
 *                        │tanggal│KAT│LOK│acak
 */
class KodeSingkat
{
    /** Kode kategori (2 huruf), dicocokkan lewat nama. Sesuai isi tabel kategori di database SI-ADUAN. */
    private const KATEGORI = [
        'standar pelayanan operasional (spo)' => 'SO',
        'etika petugas'                       => 'EP',
        'informasi layanan'                   => 'IL',
        'sarana prasarana'                    => 'SP',
        'keamanan'                            => 'KM',
        'kebersihan dan kerapian'             => 'KK',
        'biaya'                               => 'BY',
        'umum'                                => 'UM',
    ];

    /** Kode lokasi/poli bawaan (3 huruf), dicocokkan lewat nama. */
    private const LOKASI = [
        'rawat inap lt. 1'   => 'RI1',
        'rawat inap lt. 2'   => 'RI2',
        'rawat jalan'        => 'RJL',
        'igd'                => 'IGD',
        'farmasi'            => 'FAR',
        'pendaftaran'        => 'PND',
        'laboratorium'       => 'LAB',
        'radiologi'          => 'RAD',
        'area parkir & umum' => 'UMM',
    ];

    /**
     * Kode unit/poli (3 huruf) untuk nomor tiket, dibuat manual per nama supaya jelas
     * dan tidak bentrok satu sama lain — mencakup semua 98 unit di UnitSeeder.
     * Unit baru yang ditambah lewat panel admin (belum ada di daftar ini) otomatis
     * dibuatkan kode dari namanya lewat dari().
     */
    private const UNIT = [
        'apotek ranap' => 'APR',
        'gudang farmasi' => 'GFR',
        'al - afiat lt.3' => 'AA3',
        'al - afiat lt.1' => 'AA1',
        'al - husna (nicu)' => 'NIC',
        'asy-syafaah (icu)' => 'ICU',
        'asy-syifa (picu)' => 'PIC',
        'al - afiat lt.2 (nifas)' => 'AA2',
        'ar - raihan (kamar operasi)' => 'KOP',
        'darussalam' => 'DRS',
        'apotek rajal' => 'ARJ',
        'al - adn lt.3' => 'AD3',
        'hemodialisa' => 'HDS',
        'apotek igd' => 'AIG',
        'laboratorium pk' => 'LPK',
        'unit transfusi darah' => 'UTD',
        'kamar jenazah' => 'KJZ',
        'poli anak' => 'ANK',
        'poli bedah' => 'BDH',
        'poli dots' => 'DOT',
        'poli gigi dan mulut' => 'GGM',
        'poli jantung' => 'JAN',
        'poli kulit dan kelamin' => 'KUK',
        'poli mata' => 'MTA',
        'poli obgyn / kandungan' => 'OBG',
        'poli orthopedi' => 'ORT',
        'poli paru' => 'PAR',
        'poli penyakit dalam' => 'PDL',
        'poli psikiatri / jiwa' => 'JIW',
        'poli rehab narkoba' => 'RHB',
        'poli syaraf' => 'SYA',
        'poli tht' => 'THT',
        'poli vct / hiv' => 'VCT',
        'fisioterapi' => 'FIS',
        'radiologi' => 'RAD',
        'gizi' => 'GIZ',
        'kesling' => 'KSL',
        'ipsrs' => 'IPS',
        'satpam' => 'SAT',
        'cssd' => 'CSD',
        'rumah sakit balangan' => 'RSB',
        'pihak ke 3 / tim jkn' => 'JKN',
        'ibnu sina (igd)' => 'IGD',
        'ambulance' => 'AMB',
        'rekam medik' => 'RKM',
        'expired date' => 'EXP',
        'pbf' => 'PBF',
        'gudang limbah b3' => 'GLB',
        'emergency' => 'EMR',
        'gfk' => 'GFK',
        'laundry' => 'LDY',
        'kasir' => 'KAS',
        'instalasi ict' => 'ICT',
        'komite ppi' => 'PPI',
        'igd ponek' => 'PNK',
        'poliklinik 1' => 'PK1',
        'poliklinik 2' => 'PK2',
        'poli mcu' => 'MCU',
        'resepsionis' => 'RSP',
        'poli psikolog' => 'PSI',
        'al - adn lt.2' => 'AD2',
        'loket bpjs' => 'BPJ',
        'poli konservasi gigi' => 'KGI',
        'ruang direktur' => 'DIR',
        'sub. bagian kepegawaian dan sdm' => 'SDM',
        'keuangan' => 'KEU',
        'bidang administrasi umum dan keuangan' => 'AUK',
        'bidang pelayanan medik' => 'BPM',
        'bidang pelayanan non medik' => 'BPN',
        'keperawatan' => 'KPR',
        'sub. bagian perencanaan dan keuangan' => 'PRK',
        'unit pengaduan' => 'ADU',
        'loket keur' => 'LKU',
        'farmasi klinis' => 'FKL',
        'loket rawat inap' => 'LRI',
        'portal' => 'POR',
        'rawat jalan' => 'RJL',
        'instalasi farmasi' => 'IFA',
        'poli bedah saraf' => 'BSR',
        'cleaning service' => 'CSV',
        'al - adn lt.4' => 'AD4',
        'al - mukarramah (jiwa)' => 'MKJ',
        'al - adn lt.1' => 'AD1',
        'poli vaksinasi' => 'VAK',
        'generator oksigen' => 'GOK',
        'manajer pelayanan pasien (mpp)' => 'MPP',
        'sub. bagian rumah tangga hukum dan humas' => 'RTH',
        'poli bedah mulut' => 'BML',
        'laboratorium pa' => 'LPA',
        'kemoterapi' => 'KMO',
        'apotek ibs' => 'AIB',
        'kerohanian' => 'KRH',
        'laboratorium mikrobiologi' => 'LMB',
        'high care unit (hcu)' => 'HCU',
        'poli gizi' => 'PGZ',
        'cathlab' => 'CTL',
        'poli bedah onkologi' => 'BON',
        'al - husna (perinatologi)' => 'PRN',
    ];

    /** Kata yang dilewati saat kode dibuat otomatis dari nama. */
    private const KATA_UMUM = ['DAN', 'DI', 'KE', 'YANG', 'UNTUK', 'DARI', 'PELAYANAN', 'LT'];

    public static function kategori(string $nama): string
    {
        return self::KATEGORI[mb_strtolower(trim($nama))] ?? self::dari($nama, 2);
    }

    public static function lokasi(string $nama): string
    {
        return self::LOKASI[mb_strtolower(trim($nama))] ?? self::dari($nama, 3);
    }

    /**
     * Kode unit/poli untuk nomor tiket, dari tabel `unit` (98 unit/poli, lihat UnitSeeder).
     * Unit yang sudah ada di daftar UNIT di atas pakai kode itu; unit baru yang belum
     * terdaftar (mis. ditambah lewat panel admin) dibuat otomatis dari namanya (3 huruf).
     */
    public static function unit(string $nama): string
    {
        return self::UNIT[mb_strtolower(trim($nama))] ?? self::dari($nama, 3);
    }

    /**
     * Membuat kode otomatis untuk nama baru yang belum ada di daftar bawaan.
     * Dua kata atau lebih: huruf pertama tiap kata. Satu kata: huruf-huruf awalnya.
     */
    public static function dari(string $nama, int $panjang = 3): string
    {
        $bersih = strtoupper(Str::ascii($nama));
        $kata   = array_values(array_filter(
            preg_split('/[^A-Z0-9]+/', $bersih) ?: [],
            fn ($k) => $k !== '' && ! in_array($k, self::KATA_UMUM, true)
        ));

        if (count($kata) >= 2) {
            $kode = '';
            foreach (array_slice($kata, 0, $panjang) as $k) {
                $kode .= $k[0];
            }
        } else {
            $kode = substr($kata[0] ?? '', 0, $panjang);
        }

        return str_pad($kode, $panjang, 'X');
    }
}
