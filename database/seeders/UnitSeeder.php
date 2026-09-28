<?php

namespace Database\Seeders;

use App\Enums\KelompokUnit;
use App\Models\Unit;
use Illuminate\Database\Seeder;

/**
 * 98 unit/poli RSUD H. Damanhuri Barabai.
 * Sumber: Untitled.xls (kolom Kode dan Nama). Pengelompokan mengikuti UNIT_GROUPS di prototype.
 *
 * Aman dijalankan berulang: nama dan kelompok diperbarui, sedangkan penanggung jawab dan
 * nomor WhatsApp yang sudah Anda isi lewat panel admin TIDAK ditimpa.
 */
class UnitSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::data() as $baris) {
            $unit = Unit::firstOrNew(['kode' => $baris['kode']]);

            $unit->nama     = $baris['nama'];
            $unit->kelompok = $baris['kelompok'];

            if (! $unit->exists) {
                $unit->penanggung_jawab = $baris['penanggung_jawab'];
                $unit->no_wa            = $baris['no_wa'];
                $unit->aktif            = true;
            }

            $unit->save();
        }

        $this->command?->info('Unit: ' . Unit::count() . ' baris (seharusnya 98).');
    }

    /**
     * penanggung_jawab dan no_wa hanya terisi untuk 8 unit contoh dari prototype
     * (nomor DEMO -> ganti dengan nomor asli lewat menu Konfigurasi).
     * Enam unit di kelompok "Lainnya" ada di file Excel tetapi tidak ada di daftar prototype;
     * silakan nonaktifkan atau pindahkan kelompoknya bila tidak dipakai.
     *
     * @return array<int, array{kode:string,nama:string,kelompok:KelompokUnit,penanggung_jawab:?string,no_wa:?string}>
     */
    private static function data(): array
    {
        return [
            ['kode' => 'B0001', 'nama' => 'APOTEK RANAP', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0002', 'nama' => 'GUDANG FARMASI', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0003', 'nama' => 'AL - AFIAT LT.3', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0004', 'nama' => 'AL - AFIAT LT.1', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => 'Ns. Siti Aminah, S.Kep', 'no_wa' => '6281256781234'],
            ['kode' => 'B0006', 'nama' => 'AL - HUSNA (NICU)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0007', 'nama' => 'ASY-SYAFAAH (ICU)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0008', 'nama' => 'ASY-SYIFA (PICU)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0010', 'nama' => 'AL - AFIAT LT.2 (NIFAS)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0011', 'nama' => 'AR - RAIHAN (KAMAR OPERASI)', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0012', 'nama' => 'DARUSSALAM', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0014', 'nama' => 'APOTEK RAJAL', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0016', 'nama' => 'AL - ADN LT.3', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0017', 'nama' => 'HEMODIALISA', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0018', 'nama' => 'APOTEK IGD', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0019', 'nama' => 'LABORATORIUM PK', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => 'dr. Lina Marwati, Sp.PK', 'no_wa' => '6281756789012'],
            ['kode' => 'B0020', 'nama' => 'UNIT TRANSFUSI DARAH', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0021', 'nama' => 'KAMAR JENAZAH', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0022', 'nama' => 'POLI ANAK', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0024', 'nama' => 'POLI BEDAH', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0025', 'nama' => 'POLI DOTS', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0026', 'nama' => 'POLI GIGI DAN MULUT', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0027', 'nama' => 'POLI JANTUNG', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0028', 'nama' => 'POLI KULIT DAN KELAMIN', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0029', 'nama' => 'POLI MATA', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0031', 'nama' => 'POLI OBGYN / KANDUNGAN', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0032', 'nama' => 'POLI ORTHOPEDI', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0033', 'nama' => 'POLI PARU', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0034', 'nama' => 'POLI PENYAKIT DALAM', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0035', 'nama' => 'POLI PSIKIATRI / JIWA', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0037', 'nama' => 'POLI REHAB NARKOBA', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0038', 'nama' => 'POLI SYARAF', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0039', 'nama' => 'POLI THT', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0041', 'nama' => 'POLI VCT / HIV', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0042', 'nama' => 'FISIOTERAPI', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0044', 'nama' => 'RADIOLOGI', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => 'dr. Fajar Nugroho, Sp.Rad', 'no_wa' => '6281967890123'],
            ['kode' => 'B0046', 'nama' => 'GIZI', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0047', 'nama' => 'KESLING', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0048', 'nama' => 'IPSRS', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0050', 'nama' => 'SATPAM', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0051', 'nama' => 'CSSD', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0052', 'nama' => 'RUMAH SAKIT BALANGAN', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0053', 'nama' => 'PIHAK KE 3 / TIM JKN', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0054', 'nama' => 'IBNU SINA (IGD)', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => 'dr. Nurul Hidayah', 'no_wa' => '6281198765432'],
            ['kode' => 'B0055', 'nama' => 'AMBULANCE', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0056', 'nama' => 'REKAM MEDIK', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0057', 'nama' => 'EXPIRED DATE', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0059', 'nama' => 'PBF', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0060', 'nama' => 'GUDANG LIMBAH B3', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0061', 'nama' => 'EMERGENCY', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0062', 'nama' => 'GFK', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0063', 'nama' => 'LAUNDRY', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0065', 'nama' => 'KASIR', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0066', 'nama' => 'INSTALASI ICT', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0067', 'nama' => 'KOMITE PPI', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0068', 'nama' => 'IGD PONEK', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0069', 'nama' => 'POLIKLINIK 1', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0070', 'nama' => 'POLIKLINIK 2', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0071', 'nama' => 'POLI MCU', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0072', 'nama' => 'RESEPSIONIS', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0073', 'nama' => 'POLI PSIKOLOG', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0076', 'nama' => 'AL - ADN LT.2', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0080', 'nama' => 'LOKET BPJS', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => 'Budi Santoso, S.AP', 'no_wa' => '6281645678901'],
            ['kode' => 'B0081', 'nama' => 'POLI KONSERVASI GIGI', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0082', 'nama' => 'RUANG DIREKTUR', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0083', 'nama' => 'SUB. BAGIAN KEPEGAWAIAN DAN SDM', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0084', 'nama' => 'KEUANGAN', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0085', 'nama' => 'BIDANG ADMINISTRASI UMUM DAN KEUANGAN', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0086', 'nama' => 'BIDANG PELAYANAN MEDIK', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0087', 'nama' => 'BIDANG PELAYANAN NON MEDIK', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0088', 'nama' => 'KEPERAWATAN', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0089', 'nama' => 'SUB. BAGIAN PERENCANAAN DAN KEUANGAN', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0090', 'nama' => 'UNIT PENGADUAN', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => 'Hj. Rahmawati, S.Sos', 'no_wa' => '6281299990001'],
            ['kode' => 'B0091', 'nama' => 'LOKET KEUR', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0092', 'nama' => 'FARMASI KLINIS', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0094', 'nama' => 'LOKET RAWAT INAP', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0096', 'nama' => 'PORTAL', 'kelompok' => KelompokUnit::Lainnya, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0097', 'nama' => 'RAWAT JALAN', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => 'dr. Hendra Wijaya, Sp.PD', 'no_wa' => '6281323456789'],
            ['kode' => 'B0098', 'nama' => 'INSTALASI FARMASI', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => 'apt. Dewi Kurnia, S.Farm', 'no_wa' => '6281534567890'],
            ['kode' => 'B0099', 'nama' => 'POLI BEDAH SARAF', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0100', 'nama' => 'CLEANING SERVICE', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0101', 'nama' => 'AL - ADN LT.4', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0102', 'nama' => 'AL - MUKARRAMAH (JIWA)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0103', 'nama' => 'AL - ADN LT.1', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0104', 'nama' => 'POLI VAKSINASI', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0105', 'nama' => 'GENERATOR OKSIGEN', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0106', 'nama' => 'MANAJER PELAYANAN PASIEN (MPP)', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0108', 'nama' => 'SUB. BAGIAN RUMAH TANGGA HUKUM DAN HUMAS', 'kelompok' => KelompokUnit::Manajemen, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0109', 'nama' => 'POLI BEDAH MULUT', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0110', 'nama' => 'LABORATORIUM PA', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0111', 'nama' => 'KEMOTERAPI', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0112', 'nama' => 'APOTEK IBS', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0113', 'nama' => 'KEROHANIAN', 'kelompok' => KelompokUnit::Penunjang, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0115', 'nama' => 'LABORATORIUM MIKROBIOLOGI', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0116', 'nama' => 'HIGH CARE UNIT (HCU)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0117', 'nama' => 'POLI GIZI', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0118', 'nama' => 'CATHLAB', 'kelompok' => KelompokUnit::Klinis, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0119', 'nama' => 'POLI BEDAH ONKOLOGI', 'kelompok' => KelompokUnit::Poli, 'penanggung_jawab' => null, 'no_wa' => null],
            ['kode' => 'B0120', 'nama' => 'AL - HUSNA (PERINATOLOGI)', 'kelompok' => KelompokUnit::RawatInap, 'penanggung_jawab' => null, 'no_wa' => null],
        ];
    }
}
